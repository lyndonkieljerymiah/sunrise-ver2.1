@php 
    
    $data = $datasource["data"];
    
@endphp

@component('layouts.report')
    @slot('report_title')
        <div class="text-right">
            <h3>{{$datasource["title"]}}</h3>
            <h4>For the Month of {{$datasource["params"]["month"]}} - {{$datasource['params']['year']}}</h4>
        </div>
    @endslot

    @foreach($data as $key => $items)
        <p>Account No: {{$items[0]["account_no"]}}</p>
        <p>Bank Name: {{$items[0]["bank_name"]}}</p>
        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th class="text-center">Deposit Date</th>
                    <th class="text-center">Tenant</th>
                    <th class="text-center">Period</th>
                    <th class="text-center">Cheque No</th>
                    <th class="text-center">Payment For</th>
                    <th class="text-center">Payment Type</th>
                    <th class="text-center">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items[0]["payments"] as $payment)
                    <tr>
                        <td class="text-center">{{$payment["deposit_date"]}}</td>
                        <td>{{strtoupper($payment["tenant_name"])}}</td>
                        <td class="text-center">{{$payment["period_start"]}} - {{$payment["period_end"]}}</td>
                        <td class="text-center">{{$payment["payment_no"]}}</td>
                        <td class="text-center">{{strtoupper($payment["payment_mode"])}}</td>
                        <td class="text-center">{{strtoupper($payment["payment_type"])}}</td>
                        <td class="text-right">{{number_format($payment["amount"],2)}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6">Grand Total:</th>
                    <th class="text-right">{{number_format($items[0]["total_payments"],2)}}</th>
                </tr>
            </tfoot>
        </table>
    @endforeach
    
@endcomponent