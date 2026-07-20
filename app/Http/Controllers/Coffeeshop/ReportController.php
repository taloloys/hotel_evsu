<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private function buildReportQuery(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::today()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', Carbon::today()->toDateString());
        $paymentMethod = $request->input('payment_method', 'all');

        $needsArchive = Carbon::parse($dateFrom)->diffInDays(Carbon::now()) > 365;

        $orderColumns = ['order_id', 'order_number', 'tab_id', 'folio_id', 'credit_account_id', 'transaction_id', 'customer_name', 'room_number', 'status', 'discount_type', 'discount_amount', 'is_discount_percentage', 'payment_method', 'subtotal', 'total', 'user_id', 'shift_id', 'created_at', 'closed_at'];

        $query = PosOrder::with('items')
            ->where('status', 'closed')
            ->whereDate('closed_at', '>=', $dateFrom)
            ->whereDate('closed_at', '<=', $dateTo);

        if ($paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }

        if ($needsArchive) {
            $archived = DB::table('archived_pos_orders')
                ->select($orderColumns)
                ->where('status', 'closed')
                ->whereDate('closed_at', '>=', $dateFrom)
                ->whereDate('closed_at', '<=', $dateTo);

            if ($paymentMethod !== 'all') {
                $archived->where('payment_method', $paymentMethod);
            }

            $query = clone $query;
            $query = $query->select($orderColumns)->unionAll($archived);
        }

        $query->orderByDesc('closed_at');

        return [$query, $dateFrom, $dateTo, $paymentMethod, $needsArchive];
    }

    public function index(Request $request): View
    {
        [$query, $dateFrom, $dateTo, $paymentMethod, $needsArchive] = $this->buildReportQuery($request);

        $allOrders = clone $query;
        $ordersAll = $allOrders->get();

        $summary = [
            'total_sales' => (float) $ordersAll->sum('total'),
            'order_count' => $ordersAll->count(),
            'cash_total' => (float) $ordersAll->where('payment_method', 'cash')->sum('total'),
            'gcash_total' => (float) $ordersAll->where('payment_method', 'gcash')->sum('total'),
            'card_total' => (float) $ordersAll->where('payment_method', 'card')->sum('total'),
            'room_total' => (float) $ordersAll->where('payment_method', 'room_charge')->sum('total'),
        ];

        $orders = $query->paginate(10)->withQueryString();

        return view('coffeeshop.reports.index', compact('orders', 'summary', 'dateFrom', 'dateTo', 'paymentMethod'));
    }

    public function export(Request $request): StreamedResponse
    {
        [$query, $dateFrom, $dateTo, $paymentMethod] = $this->buildReportQuery($request);

        $orders = $query->get();

        $filename = 'pos_sales_report_'.$dateFrom.'_to_'.$dateTo.'.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $output = fopen('php://output', 'w');

            $xmlHeader = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
                '<?mso-application progid="Excel.Sheet"?>'."\n".
                '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'."\n".
                ' xmlns:o="urn:schemas-microsoft-com:office:office"'."\n".
                ' xmlns:x="urn:schemas-microsoft-com:office:excel"'."\n".
                ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"'."\n".
                ' xmlns:html="http://www.w3.org/TR/REC-html40">'."\n".
                ' <Styles>'."\n".
                '  <Style ss:ID="Header">'."\n".
                '   <Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="11"/>'."\n".
                '   <Interior ss:Color="#4E342E" ss:Pattern="Solid"/>'."\n".
                '   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>'."\n".
                '   <Borders>'."\n".
                '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#2F1C16"/>'."\n".
                '   </Borders>'."\n".
                '  </Style>'."\n".
                '  <Style ss:ID="DataCell">'."\n".
                '   <Alignment ss:Vertical="Center"/>'."\n".
                '   <Borders>'."\n".
                '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>'."\n".
                '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>'."\n".
                '   </Borders>'."\n".
                '  </Style>'."\n".
                '  <Style ss:ID="CenterCell">'."\n".
                '   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>'."\n".
                '   <Borders>'."\n".
                '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>'."\n".
                '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>'."\n".
                '   </Borders>'."\n".
                '  </Style>'."\n".
                '  <Style ss:ID="CurrencyCell">'."\n".
                '   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>'."\n".
                '   <NumberFormat ss:Format="#,##0.00"/>'."\n".
                '   <Borders>'."\n".
                '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>'."\n".
                '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>'."\n".
                '   </Borders>'."\n".
                '  </Style>'."\n".
                '  <Style ss:ID="DateStyle">'."\n".
                '   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>'."\n".
                '   <NumberFormat ss:Format="mmm dd, yyyy h:mm AM/PM"/>'."\n".
                '   <Borders>'."\n".
                '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>'."\n".
                '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>'."\n".
                '   </Borders>'."\n".
                '  </Style>'."\n".
                ' </Styles>'."\n".
                ' <Worksheet ss:Name="POS Sales Report">'."\n".
                '  <Table>'."\n".
                '   <Column ss:Width="18" ss:AutoFitWidth="1"/>'."\n".
                '   <Column ss:Width="28" ss:AutoFitWidth="1"/>'."\n".
                '   <Column ss:Width="16" ss:AutoFitWidth="1"/>'."\n".
                '   <Column ss:Width="20" ss:AutoFitWidth="1"/>'."\n".
                '   <Column ss:Width="20" ss:AutoFitWidth="1"/>'."\n".
                '   <Column ss:Width="26" ss:AutoFitWidth="1"/>'."\n".
                '   <Row ss:Height="26" ss:StyleID="Header">'."\n".
                '    <Cell><Data ss:Type="String">Order Number</Data></Cell>'."\n".
                '    <Cell><Data ss:Type="String">Customer Name</Data></Cell>'."\n".
                '    <Cell><Data ss:Type="String">Room Number</Data></Cell>'."\n".
                '    <Cell><Data ss:Type="String">Payment Method</Data></Cell>'."\n".
                '    <Cell><Data ss:Type="String">Total Amount (₱)</Data></Cell>'."\n".
                '    <Cell><Data ss:Type="String">Closed At</Data></Cell>'."\n".
                '   </Row>'."\n";

            fwrite($output, $xmlHeader);

            foreach ($orders as $order) {
                $orderNum = htmlspecialchars($order->order_number ?? 'N/A');
                $customer = htmlspecialchars($order->customer_name ?? 'Walk-in');
                $room = htmlspecialchars($order->room_number ?? '—');
                $payment = htmlspecialchars(ucwords(str_replace('_', ' ', $order->payment_method ?? 'Cash')));
                $total = (float) $order->total;

                $closedAtRaw = $order->closed_at ?: $order->created_at;
                $closedAtFormatted = $closedAtRaw
                    ? Carbon::parse($closedAtRaw)->format('Y-m-d\TH:i:s')
                    : '';

                $rowXml = '   <Row ss:Height="20">'."\n".
                    '    <Cell ss:StyleID="CenterCell"><Data ss:Type="String">'.$orderNum.'</Data></Cell>'."\n".
                    '    <Cell ss:StyleID="DataCell"><Data ss:Type="String">'.$customer.'</Data></Cell>'."\n".
                    '    <Cell ss:StyleID="CenterCell"><Data ss:Type="String">'.$room.'</Data></Cell>'."\n".
                    '    <Cell ss:StyleID="CenterCell"><Data ss:Type="String">'.$payment.'</Data></Cell>'."\n".
                    '    <Cell ss:StyleID="CurrencyCell"><Data ss:Type="Number">'.$total.'</Data></Cell>'."\n";

                if (! empty($closedAtFormatted)) {
                    $rowXml .= '    <Cell ss:StyleID="DateStyle"><Data ss:Type="DateTime">'.$closedAtFormatted.'</Data></Cell>'."\n";
                } else {
                    $rowXml .= '    <Cell ss:StyleID="CenterCell"><Data ss:Type="String">—</Data></Cell>'."\n";
                }

                $rowXml .= '   </Row>'."\n";

                fwrite($output, $rowXml);
            }

            $xmlFooter = '  </Table>'."\n".
                ' </Worksheet>'."\n".
                '</Workbook>';

            fwrite($output, $xmlFooter);
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
