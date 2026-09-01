<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Cache-control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Tax Invoice #{{ $invoice->increment_id ?? $invoice->id }}</title>

        <style type="text/css">
            @page {
                margin: 6mm 6mm 6mm 6mm;
                size: a4 portrait;
            }

            * {
                box-sizing: border-box;
                font-family: 'DejaVu Sans', 'Arial', sans-serif;
                color: #000000;
            }

            body {
                font-size: 8pt;
                line-height: 1.25;
                margin: 0;
                padding: 0;
                background-color: #ffffff;
                color: #000000;
            }

            /* Tally outer double/single frame */
            .tally-box {
                width: 100%;
                border: 1.5px solid #000000;
                margin: 0 auto;
            }

            .tally-table {
                width: 100%;
                border-collapse: collapse;
            }

            .tally-table td, .tally-table th {
                border: 1px solid #000000;
                padding: 4px 6px;
                vertical-align: top;
            }

            .no-border-top { border-top: none !important; }
            .no-border-bottom { border-bottom: none !important; }
            .no-border-left { border-left: none !important; }
            .no-border-right { border-right: none !important; }

            /* Header Section */
            .main-title {
                text-align: center;
                font-size: 11pt;
                font-weight: bold;
                letter-spacing: 1px;
                padding: 3px 0;
                border-bottom: 1.5px solid #000000;
                text-transform: uppercase;
            }

            .sub-title {
                font-size: 7.5pt;
                font-weight: normal;
                margin-top: 1px;
            }

            .company-name {
                font-size: 11pt;
                font-weight: bold;
                margin-bottom: 2px;
            }

            .company-details {
                font-size: 7.5pt;
                line-height: 1.25;
            }

            .party-header {
                font-weight: bold;
                font-size: 7.5pt;
                text-decoration: underline;
                margin-bottom: 3px;
                display: block;
            }

            .party-title {
                font-size: 9pt;
                font-weight: bold;
                margin-bottom: 2px;
            }

            /* Items Table in Tally Style */
            .items-header th {
                background-color: #f2f2f2;
                font-size: 7.5pt;
                font-weight: bold;
                text-align: center;
                padding: 4px 3px;
                border: 1px solid #000000;
                text-transform: uppercase;
            }

            .item-row td {
                border-left: 1px solid #000000;
                border-right: 1px solid #000000;
                border-top: none;
                border-bottom: none;
                padding: 4px 4px;
                font-size: 8pt;
            }

            .item-description {
                font-weight: bold;
                color: #000000;
            }

            .item-subtext {
                font-size: 7pt;
                color: #333333;
                margin-top: 1px;
            }

            .text-left { text-align: left; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .bold { font-weight: bold; }

            /* Tally Tax Summary Table */
            .hsn-summary-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 4px;
            }

            .hsn-summary-table th, .hsn-summary-table td {
                border: 1px solid #000000;
                font-size: 7pt;
                padding: 3px 4px;
                text-align: right;
            }

            .hsn-summary-table th {
                background-color: #f2f2f2;
                text-align: center;
                font-weight: bold;
            }

            /* Amount in words */
            .amount-words {
                font-size: 8pt;
                font-style: italic;
                padding: 4px 6px;
                border-top: 1px solid #000000;
                border-bottom: 1px solid #000000;
            }

            /* Footer signature */
            .footer-sign-table {
                width: 100%;
                border-collapse: collapse;
            }

            .footer-sign-table td {
                border: 1px solid #000000;
                padding: 6px 8px;
                vertical-align: top;
                font-size: 7.5pt;
            }

            .declaration {
                font-size: 7pt;
                line-height: 1.2;
            }
        </style>
    </head>

    <body>
        @php
            $order = $invoice->order;
            $isGst = (bool) ($order->is_gst_invoice ?? false);
            $gstTaxType = $order->gst_tax_type ?? 'intra_state';
            $isInterState = ($gstTaxType === 'inter_state');

            $storeName = core()->getConfigData('sales.shipping.origin.store_name') ?: config('app.name', 'Himalayan Water Science');
            $storeAddress1 = core()->getConfigData('sales.shipping.origin.address1');
            $storeCity = core()->getConfigData('sales.shipping.origin.city');
            $storeState = core()->getConfigData('sales.shipping.origin.state');
            $storeZipcode = core()->getConfigData('sales.shipping.origin.zipcode');
            $storeCountry = core()->country_name(core()->getConfigData('sales.shipping.origin.country'));
            $storeGstin = core()->getConfigData('sales.shipping.origin.vat_number');
            $storeContact = core()->getConfigData('sales.shipping.origin.contact');
            $storeBank = core()->getConfigData('sales.shipping.origin.bank_details');

            $posState = $order->gst_place_of_supply ?: optional($order->shipping_address ?: $order->billing_address)->state ?: $storeState;

            // Helper for Number to Words (Indian format)
            if (!function_exists('hws_number_to_words')) {
                function hws_number_to_words($number) {
                    $no = floor($number);
                    $point = round($number - $no, 2) * 100;
                    $hundred = null;
                    $digits_1 = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
                    $digits_2 = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
                    $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
                    $str = [];
                    
                    if ($no == 0) {
                        return 'Zero Rupees Only';
                    }

                    $i = 0;
                    while ($i < 5 && $no > 0) {
                        $divider = ($i == 1) ? 10 : 100;
                        if ($i == 0) {
                            $number_part = $no % 1000;
                            $no = floor($no / 1000);
                        } else {
                            $number_part = $no % $divider;
                            $no = floor($no / $divider);
                        }

                        if ($number_part) {
                            $plural = (($counter = count($str)) && $number_part > 9) ? '' : '';
                            $hundred = ($counter == 0 && $str) ? ' and ' : '';
                            
                            $p1 = floor($number_part / 100);
                            $p2 = $number_part % 100;
                            $part_str = '';
                            if ($p1) {
                                $part_str .= $digits_1[$p1] . ' Hundred ';
                            }
                            if ($p2 < 20) {
                                $part_str .= $digits_1[$p2];
                            } else {
                                $part_str .= $digits_2[floor($p2 / 10)] . ' ' . $digits_1[$p2 % 10];
                            }
                            
                            $str[] = $part_str . ' ' . ($digits[$i] ?? '') . $plural . ' ' . $hundred;
                        }
                        $i++;
                    }

                    $result = implode('', array_reverse($str));
                    $points = ($point) ? " and " . ($digits_1[floor($point / 10)] . " " . $digits_1[$point % 10]) . " Paise" : '';
                    return 'INR ' . trim(preg_replace('/\s+/', ' ', $result)) . " Rupees" . $points . " Only";
                }
            }
        @endphp

        <div class="tally-box">

            {{-- 1. Main Header Title Bar (Tally Standard) --}}
            <div class="main-title">
                Tax Invoice
                <div class="sub-title">( e-Invoice / Standard GST Format )</div>
            </div>

            {{-- 2. Tally 2-Column Info Grid (Seller / Invoice Details) --}}
            <table class="tally-table" style="border: none;">
                <tr>
                    {{-- Left: Seller Details --}}
                    <td style="width: 50%; border-top: none; border-left: none;">
                        <div class="company-name">{{ $storeName }}</div>
                        <div class="company-details">
                            @if($storeAddress1){{ $storeAddress1 }},<br>@endif
                            @if($storeCity){{ $storeCity }}@endif @if($storeState) - {{ $storeState }}@endif @if($storeZipcode) ({{ $storeZipcode }})@endif<br>
                            @if($storeCountry)Country: {{ $storeCountry }}<br>@endif
                            @if($storeGstin)<strong>GSTIN/UIN:</strong> {{ $storeGstin }}<br>@endif
                            @if($storeState)<strong>State Name:</strong> {{ $storeState }}<br>@endif
                            @if($storeContact)<strong>Contact:</strong> {{ $storeContact }}@endif
                        </div>
                    </td>

                    {{-- Right: Invoice & Transportation Info Table (Tally Standard Layout) --}}
                    <td style="width: 50%; padding: 0; border-top: none; border-right: none;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 50%; border-top: none; border-left: none; font-size: 7.5pt;">
                                    <strong>Invoice No.</strong><br>
                                    <span style="font-size: 8.5pt; font-weight: bold;">#{{ $invoice->increment_id ?? $invoice->id }}</span>
                                </td>
                                <td style="width: 50%; border-top: none; border-right: none; font-size: 7.5pt;">
                                    <strong>Dated</strong><br>
                                    <span style="font-size: 8.5pt; font-weight: bold;">{{ core()->formatDate($invoice->created_at, 'd-M-Y') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-left: none; font-size: 7.5pt;">
                                    <strong>Delivery Note</strong><br>
                                    <span>—</span>
                                </td>
                                <td style="border-right: none; font-size: 7.5pt;">
                                    <strong>Mode/Terms of Payment</strong><br>
                                    <span>{{ core()->getConfigData('sales.paymentmethods.' . $order->payment->method . '.title') ?: ucwords(str_replace('_', ' ', $order->payment->method)) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-left: none; font-size: 7.5pt;">
                                    <strong>Buyer's Order No.</strong><br>
                                    <span>#{{ $order->increment_id }}</span>
                                </td>
                                <td style="border-right: none; font-size: 7.5pt;">
                                    <strong>Dated</strong><br>
                                    <span>{{ $order->created_at ? $order->created_at->format('d-M-Y') : '' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-left: none; border-bottom: none; font-size: 7.5pt;">
                                    <strong>Despatch Doc No.</strong><br>
                                    <span>—</span>
                                </td>
                                <td style="border-right: none; border-bottom: none; font-size: 7.5pt;">
                                    <strong>Place of Supply</strong><br>
                                    <span style="font-weight: bold;">{{ $posState }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- 3. Consignee (Ship To) & Buyer (Bill To) Row --}}
                <tr>
                    {{-- Buyer / Bill to --}}
                    <td style="width: 50%; border-left: none;">
                        <span class="party-header">Buyer (Bill to)</span>
                        @if ($order->billing_address)
                            <div class="party-title">{{ $order->billing_company_name ?: ($order->billing_address->company_name ?: $order->billing_address->name) }}</div>
                            <div class="company-details">
                                @if($order->billing_company_name && $order->billing_address->name)Attn: {{ $order->billing_address->name }}<br>@endif
                                {{ $order->billing_address->address1 }}<br>
                                {{ $order->billing_address->city }}, {{ $order->billing_address->state }} - {{ $order->billing_address->postcode }}<br>
                                <strong>GSTIN/UIN:</strong> {{ $order->gstin ?: 'Unregistered' }}<br>
                                <strong>State Name:</strong> {{ $order->billing_address->state }}<br>
                                <strong>Contact:</strong> {{ $order->billing_address->phone }}
                            </div>
                        @endif
                    </td>

                    {{-- Consignee / Ship to --}}
                    <td style="width: 50%; border-right: none;">
                        <span class="party-header">Consignee (Ship to)</span>
                        @if ($order->shipping_address)
                            <div class="party-title">{{ $order->shipping_address->company_name ?: $order->shipping_address->name }}</div>
                            <div class="company-details">
                                @if($order->shipping_address->company_name && $order->shipping_address->name)Attn: {{ $order->shipping_address->name }}<br>@endif
                                {{ $order->shipping_address->address1 }}<br>
                                {{ $order->shipping_address->city }}, {{ $order->shipping_address->state }} - {{ $order->shipping_address->postcode }}<br>
                                <strong>State Name:</strong> {{ $order->shipping_address->state }}<br>
                                <strong>Contact:</strong> {{ $order->shipping_address->phone }}
                            </div>
                        @else
                            <div class="company-details" style="font-style: italic;">Same as Buyer (Bill to)</div>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- 4. Tally Items Grid --}}
            <table class="tally-table" style="border-left: none; border-right: none; margin-top: -1px;">
                <thead>
                    <tr class="items-header">
                        <th style="width: 4%;">Sl No.</th>
                        <th style="width: 44%;" class="text-left">Description of Goods</th>
                        <th style="width: 12%;">HSN/SAC</th>
                        <th style="width: 8%;">Quantity</th>
                        <th style="width: 14%;">Rate (INR)</th>
                        <th style="width: 6%;">per</th>
                        <th style="width: 12%;">Amount (INR)</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $sl = 1; 
                        $totalQty = 0;
                    @endphp
                    @foreach ($invoice->items as $item)
                        @php
                            $hsn = $item->hsn_code ?: '-';
                            $rate = $item->base_price;
                            $taxable = $item->base_total;
                            $totalQty += $item->qty;
                        @endphp
                        <tr class="item-row">
                            <td class="text-center">{{ $sl++ }}</td>
                            <td class="text-left">
                                <div class="item-description">{{ $item->name }}</div>
                                <div class="item-subtext">SKU: {{ $item->getTypeInstance()->getOrderedItem($item)->sku }}</div>
                                @if (isset($item->additional['attributes']))
                                    <div class="item-subtext">
                                        @foreach ($item->additional['attributes'] as $attribute)
                                            <span>{{ $attribute['attribute_name'] }}: {{ $attribute['option_label'] }}</span>@if(!$loop->last), @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="text-center bold">{{ $hsn }}</td>
                            <td class="text-center bold">{{ $item->qty }}</td>
                            <td class="text-right">{!! number_format($rate, 2) !!}</td>
                            <td class="text-center">NOS</td>
                            <td class="text-right bold">{!! number_format($taxable, 2) !!}</td>
                        </tr>
                    @endforeach

                    {{-- GST Tax Rows rendered inside items ledger like Tally --}}
                    @if($isGst && $isInterState)
                        <tr class="item-row">
                            <td></td>
                            <td class="text-left" style="font-weight: bold; font-style: italic;">OUTPUT IGST</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-right"></td>
                            <td class="text-center"></td>
                            <td class="text-right bold">{!! number_format($invoice->base_tax_amount, 2) !!}</td>
                        </tr>
                    @elseif($isGst)
                        <tr class="item-row">
                            <td></td>
                            <td class="text-left" style="font-weight: bold; font-style: italic;">OUTPUT CGST</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-right"></td>
                            <td class="text-center"></td>
                            <td class="text-right bold">{!! number_format($invoice->base_tax_amount / 2, 2) !!}</td>
                        </tr>
                        <tr class="item-row">
                            <td></td>
                            <td class="text-left" style="font-weight: bold; font-style: italic;">OUTPUT SGST</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-right"></td>
                            <td class="text-center"></td>
                            <td class="text-right bold">{!! number_format($invoice->base_tax_amount / 2, 2) !!}</td>
                        </tr>
                    @endif

                    @if($invoice->base_shipping_amount > 0)
                        <tr class="item-row">
                            <td></td>
                            <td class="text-left" style="font-style: italic;">Freight & Shipping Charges</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-right"></td>
                            <td class="text-center"></td>
                            <td class="text-right bold">{!! number_format($invoice->base_shipping_amount, 2) !!}</td>
                        </tr>
                    @endif

                    @if($invoice->base_discount_amount > 0)
                        <tr class="item-row">
                            <td></td>
                            <td class="text-left" style="font-style: italic;">Discount / Rebate</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-right"></td>
                            <td class="text-center"></td>
                            <td class="text-right bold">-{!! number_format($invoice->base_discount_amount, 2) !!}</td>
                        </tr>
                    @endif

                    {{-- Spacer row --}}
                    <tr class="item-row" style="height: 40px;">
                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>

                    {{-- Items Total Row --}}
                    <tr style="border-top: 1.5px solid #000000; border-bottom: 1.5px solid #000000; font-weight: bold; background: #fafafa;">
                        <td></td>
                        <td class="text-right bold">Total</td>
                        <td></td>
                        <td class="text-center bold">{{ $totalQty }} NOS</td>
                        <td></td>
                        <td></td>
                        <td class="text-right bold" style="font-size: 9pt;">₹{!! number_format($invoice->base_grand_total, 2) !!}</td>
                    </tr>
                </tbody>
            </table>

            {{-- 5. Amount in Words --}}
            <div class="amount-words">
                <strong>Amount Chargeable (in words):</strong><br>
                <span style="font-weight: bold; text-transform: uppercase;">{{ hws_number_to_words($invoice->base_grand_total) }}</span>
            </div>

            {{-- 6. Tally HSN/SAC Tax Breakdown Matrix --}}
            <div style="padding: 4px 6px;">
                <table class="hsn-summary-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align: middle;">HSN/SAC</th>
                            <th rowspan="2" style="vertical-align: middle;">Taxable Value (INR)</th>
                            @if($isGst && $isInterState)
                                <th colspan="2">Integrated Tax (IGST)</th>
                            @elseif($isGst)
                                <th colspan="2">Central Tax (CGST)</th>
                                <th colspan="2">State Tax (SGST)</th>
                            @else
                                <th colspan="2">Tax</th>
                            @endif
                            <th rowspan="2" style="vertical-align: middle;">Total Tax Amount (INR)</th>
                        </tr>
                        <tr>
                            @if($isGst && $isInterState)
                                <th>Rate</th>
                                <th>Amount</th>
                            @elseif($isGst)
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Rate</th>
                                <th>Amount</th>
                            @else
                                <th>Rate</th>
                                <th>Amount</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center bold">As Listed</td>
                            <td class="text-right">{!! number_format($invoice->base_sub_total, 2) !!}</td>
                            @if($isGst && $isInterState)
                                <td class="text-center">18%</td>
                                <td class="text-right">{!! number_format($invoice->base_tax_amount, 2) !!}</td>
                            @elseif($isGst)
                                <td class="text-center">9%</td>
                                <td class="text-right">{!! number_format($invoice->base_tax_amount / 2, 2) !!}</td>
                                <td class="text-center">9%</td>
                                <td class="text-right">{!! number_format($invoice->base_tax_amount / 2, 2) !!}</td>
                            @else
                                <td class="text-center">—</td>
                                <td class="text-right">{!! number_format($invoice->base_tax_amount, 2) !!}</td>
                            @endif
                            <td class="text-right bold">{!! number_format($invoice->base_tax_amount, 2) !!}</td>
                        </tr>
                        <tr style="font-weight: bold; background-color: #fafafa;">
                            <td class="text-right bold">Total:</td>
                            <td class="text-right bold">{!! number_format($invoice->base_sub_total, 2) !!}</td>
                            @if($isGst && $isInterState)
                                <td></td>
                                <td class="text-right bold">{!! number_format($invoice->base_tax_amount, 2) !!}</td>
                            @elseif($isGst)
                                <td></td>
                                <td class="text-right bold">{!! number_format($invoice->base_tax_amount / 2, 2) !!}</td>
                                <td></td>
                                <td class="text-right bold">{!! number_format($invoice->base_tax_amount / 2, 2) !!}</td>
                            @else
                                <td></td>
                                <td class="text-right bold">{!! number_format($invoice->base_tax_amount, 2) !!}</td>
                            @endif
                            <td class="text-right bold">{!! number_format($invoice->base_tax_amount, 2) !!}</td>
                        </tr>
                    </tbody>
                </table>
                <div style="font-size: 7pt; margin-top: 2px;">
                    <strong>Tax Amount (in words):</strong> {{ hws_number_to_words($invoice->base_tax_amount) }}
                </div>
            </div>

            {{-- 7. Tally Bank & Signatures Footer --}}
            <table class="footer-sign-table" style="border-left: none; border-right: none; border-bottom: none;">
                <tr>
                    {{-- Bank Details & Terms --}}
                    <td style="width: 55%; border-left: none;">
                        @if($storeBank)
                            <strong>Company's Bank Details:</strong><br>
                            <span style="font-size: 7pt;">{!! nl2br(e($storeBank)) !!}</span>
                            <br><br>
                        @endif
                        <strong>Declaration:</strong>
                        <div class="declaration">
                            We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.
                        </div>
                    </td>

                    {{-- Authorised Signatory Box --}}
                    <td style="width: 45%; text-align: right; border-right: none;">
                        <strong>For {{ $storeName }}</strong>
                        <div style="height: 50px;"></div>
                        <div style="font-weight: bold; border-top: 1px solid #000000; display: inline-block; padding-top: 2px;">
                            Authorised Signatory
                        </div>
                    </td>
                </tr>
            </table>

        </div>
        <div style="text-align: center; font-size: 6.5pt; margin-top: 3px; color: #444;">
            This is a Computer Generated Invoice
        </div>
    </body>
</html>
