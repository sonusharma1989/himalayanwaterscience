<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->quote_no }}</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        
        /* Header section styling exactly matching the Rama Sales Corporation template layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .header-logo-side {
            width: 45%;
            vertical-align: middle;
            text-align: left;
        }
        
        /* CSS-based Green Circular Leaf Logo to avoid unicode rendering bugs */
        .logo-circle {
            width: 32px;
            height: 32px;
            background: #15803d;
            border-radius: 16px;
            display: inline-block;
            vertical-align: middle;
            text-align: center;
        }
        .logo-inner {
            color: #fff;
            font-weight: bold;
            font-size: 15px;
            line-height: 32px;
        }
        .logo-text-wrapper {
            display: inline-block;
            vertical-align: middle;
            margin-left: 8px;
        }
        .logo-title {
            font-size: 15px;
            font-weight: bold;
            color: #000;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .logo-subtitle {
            font-size: 7.5px;
            color: #475569;
            margin: 0;
        }

        .header-title-side {
            width: 55%;
            text-align: right;
            vertical-align: middle;
        }
        .corp-title {
            font-size: 26px;
            font-family: 'Times-Roman', Times, serif;
            font-style: italic;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .corp-address {
            font-size: 9.5px;
            font-style: italic;
            color: #334155;
            margin: 2px 0 0;
        }
        .corp-contact {
            font-size: 9.5px;
            color: #000;
            margin: 2px 0 0;
        }
        .corp-subtitle {
            font-size: 9px;
            color: #16a34a;
            font-weight: bold;
            margin: 3px 0 0;
        }
        
        /* Double underline divider under header */
        .header-divider {
            border-top: 1px solid #000;
            border-bottom: 2px solid #000;
            height: 2px;
            margin: 4px 0 10px;
        }

        /* Authorized Dealers Row style matching image */
        .dealers-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .dealers-table td {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #334155;
            padding: 5px;
        }
        .dealer-xylem { font-family: 'Times-Roman', serif; font-style: italic; color: #0284c7; }
        .dealer-emerson { font-family: sans-serif; font-weight: bold; color: #1e3a8a; }
        .dealer-cri { font-family: 'Times-Roman', serif; font-weight: bold; color: #b91c1c; }
        .dealer-ambrojal { font-family: 'Georgia', serif; font-style: italic; color: #0f766e; }
        
        /* Quotation Metadata details */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .meta-table td {
            padding: 3px 0;
            font-size: 11.5px;
        }
        .bold-label {
            font-weight: bold;
        }

        /* Items table styling matching the exact grid template */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .items-table th {
            border: 1px solid #000;
            background: #fff;
            color: #000;
            font-weight: bold;
            font-size: 11px;
            padding: 5px;
            text-align: left;
        }
        .items-table td {
            border: 1px solid #000;
            padding: 8px 5px;
            font-size: 10.5px;
            color: #000;
        }
        .total-highlight {
            background: #22c55e;
            color: #000 !important;
            font-weight: bold;
            text-align: right;
        }
        
        /* Terms and signature section */
        .terms-section {
            width: 60%;
            float: left;
            margin-top: 15px;
        }
        .terms-title {
            font-weight: bold;
            margin-bottom: 4px;
        }
        .terms-item {
            margin-bottom: 2px;
            font-size: 10px;
        }
        
        .signature-section {
            width: 35%;
            float: right;
            text-align: right;
            margin-top: 35px;
            font-size: 10.5px;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 35px;
        }

        .clearfix {
            clear: both;
        }

        /* Footer brand badges styling */
        .footer-badges-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border-top: 2px solid #000;
            padding-top: 5px;
        }
        .badge-td {
            text-align: center;
            padding: 4px;
        }
        .badge-box {
            border: 1px solid #000;
            padding: 2px 6px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 2px;
            display: inline-block;
        }
        
        .footer-desc-text {
            font-size: 8px;
            color: #000;
            text-align: center;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.1px;
            line-height: 1.3;
            border-top: 1px solid #000;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td class="header-logo-side">
                <div class="logo-circle">
                    <span class="logo-inner">H</span>
                </div>
                <div class="logo-text-wrapper">
                    <div class="logo-title">HIMALAYAN GROUP</div>
                    <div class="logo-subtitle">Pure Water Solutions & Engineering Services</div>
                </div>
            </td>
            <td class="header-title-side">
                <h1 class="corp-title">Himalayan Water Science</h1>
                <p class="corp-address">Rajpur Road, Dehradun, Uttarakhand, India</p>
                <p class="corp-contact">Email: info@hws.in | Web: www.hws.in</p>
                <p class="corp-subtitle">AUTHORIZED WATER TREATMENT ENGINEERING PARTNER</p>
            </td>
        </tr>
    </table>
    
    <!-- Double Horizontal Line Underneath Header -->
    <div class="header-divider"></div>

    <!-- Dealer Logos Row -->
    <table class="dealers-table">
        <tr>
            <td class="dealer-xylem">xylem</td>
            <td class="dealer-emerson">EMERSON</td>
            <td class="dealer-cri">C.R.I. PUMPS</td>
            <td class="dealer-ambrojal">Ambrojal</td>
        </tr>
    </table>

    <!-- Quotation Details -->
    <table class="meta-table">
        <tr>
            <td style="width: 50%; font-size: 12px;">
                <span class="bold-label">Quotation: -</span> HWS/SO/{{ $quotation->quote_no }}/2026-27
            </td>
            <td style="width: 50%; text-align: right; font-size: 12px;">
                <span class="bold-label">Date: -</span> {{ $quotation->created_at->format('d/m/Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 12px;">
                <span class="bold-label">Client Name: -</span> {{ $quotation->customer_name }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 12px;">
                <span class="bold-label">Installation Site: -</span> {{ $quotation->customer_address }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 12px;">
                <span class="bold-label">Subject: -</span> For {{ $quotation->customer_name }} @if($quotation->items->first()) {{ $quotation->items->first()->item_name }} @endif
            </td>
        </tr>
    </table>

    <p style="font-size: 11px; margin-top: 10px;">Dear Sir,</p>

    <!-- Items Grid Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">Sr No</th>
                <th style="width: 47%;">Particulars</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 10%; text-align: center;">Unit</th>
                <th style="width: 12%; text-align: right;">Rate</th>
                <th style="width: 13%; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}.</td>
                    <td>{{ $item->item_name }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: center;">nos.</td>
                    <td style="text-align: right;">{{ number_format($item->rate, 2) }}/-</td>
                    <td style="text-align: right;">{{ number_format($item->amount, 2) }}/-</td>
                </tr>
            @endforeach
            
            <!-- GST extra row -->
            <tr>
                <td style="text-align: center;"></td>
                <td>GST extra</td>
                <td style="text-align: center;"></td>
                <td style="text-align: center;"></td>
                <td style="text-align: right; font-weight: bold;">18%</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($quotation->tax_amount, 2) }}/-</td>
            </tr>

            <!-- Grand Total Row -->
            <tr>
                <td style="text-align: center;"></td>
                <td style="font-weight: bold;">Total</td>
                <td style="text-align: center;"></td>
                <td style="text-align: center;"></td>
                <td style="text-align: right;"></td>
                <td class="total-highlight">{{ number_format($quotation->grand_total, 2) }}/-</td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 11px; margin-bottom: 12px;">Please find the attached quotation for required items.</p>

    <!-- Terms & Signature section -->
    <div>
        <div class="terms-section">
            <div class="terms-title">TERMS & CONDITIONS: -</div>
            <div class="terms-item"><span class="bold-label">TERMS OF PAYMENT:</span> 100% as advance</div>
            <div class="terms-item"><span class="bold-label">GST:</span> -18% EXTRA.</div>
            <div class="terms-item"><span class="bold-label">TRANSPORTATION:</span> Extra as Actual</div>
            <div class="terms-item"><span class="bold-label">VALIDITY:</span> This offer is valid for 15 days.</div>
            <div class="terms-item"><span class="bold-label">Delivery:</span> - Ready stock Next Day Delivery after Confirmation.</div>
        </div>
        
        <div class="signature-section">
            <div class="signature-title">FOR, HIMALAYAN WATER SCIENCE</div>
            <div style="font-weight: bold;">Authorized Signatory</div>
        </div>
    </div>
    
    <div class="clearfix"></div>

    <!-- Bottom Footer Badges and text -->
    <table class="footer-badges-table">
        <tr>
            <td class="badge-td"><span class="badge-box">RoHS Compliant</span></td>
            <td class="badge-td"><span class="badge-box">Make In India</span></td>
            <td class="badge-td"><span class="badge-box">MSME</span></td>
            <td class="badge-td"><span class="badge-box">ISO 9001:2015</span></td>
            <td class="badge-td"><span class="badge-box">ISO Certified</span></td>
        </tr>
    </table>
    
    <p class="footer-desc-text">
        SEWERAGE TREATMENT PLANT, EFFLUENT TREATMENT PLANT, WATER TREATMENT PLANT, R.O.SYSTEM, SOFTNER<br>
        PLANT, D.M. PLANT, GREY WATER TREATMENT PLANT, WATER COOLERS, HOT WATER TANKS, HEAT PUMPS, HVSE, AND ALL<br>
        TYPE OF TRUNKEY PROJECTS AND SWIMMING POOL ALSO.
    </p>

</body>
</html>
