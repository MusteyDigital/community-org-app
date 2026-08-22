<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contribution Receipt</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #1a1a1a; font-size: 13px; }
        .header { border-bottom: 3px solid #0f3d3e; padding-bottom: 16px; margin-bottom: 24px; }
        .org-name { font-size: 22px; font-weight: bold; color: #0f3d3e; margin: 0; }
        .receipt-title { font-size: 14px; color: #666; margin-top: 4px; letter-spacing: 1px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 10px 0; border-bottom: 1px solid #eee; }
        .label { color: #666; width: 40%; }
        .value { font-weight: bold; text-align: right; }
        .amount-row td { padding: 16px 0; font-size: 18px; }
        .amount-row .value { color: #0f3d3e; }
        .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #eee; font-size: 11px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <p class="org-name">{{ $contribution->organization->name ?? 'Community Org' }}</p>
        <p class="receipt-title">Contribution Receipt</p>
    </div>

    <table>
        <tr>
            <td class="label">Receipt No.</td>
            <td class="value">#{{ str_pad($contribution->id, 6, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td class="label">Contributor</td>
            <td class="value">{{ $contribution->member->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Category</td>
            <td class="value">{{ ucfirst($contribution->category) }}</td>
        </tr>
        <tr>
            <td class="label">Date</td>
            <td class="value">{{ $contribution->contributed_at->format('F j, Y') }}</td>
        </tr>
        @if($contribution->payment_reference)
        <tr>
            <td class="label">Payment Reference</td>
            <td class="value">{{ $contribution->payment_reference }}</td>
        </tr>
        @endif
        @if($contribution->note)
        <tr>
            <td class="label">Note</td>
            <td class="value">{{ $contribution->note }}</td>
        </tr>
        @endif
        <tr class="amount-row">
            <td class="label">Amount</td>
            <td class="value">NGN {{ number_format($contribution->amount, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        This receipt was generated automatically and confirms the contribution recorded above.<br>
        Generated on {{ now()->format('F j, Y') }}
    </div>
</body>
</html>
