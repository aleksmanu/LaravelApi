<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .main-container {
            margin: 0;
            color: #333;
            width: 100%;
            max-height: 100%;
            font-size: 14px;
            font-family: 'sans-serif';
        }

        .title {
            font-size: 30px;
            text-transform: uppercase;
        }

        .big-logo {
            background-color: #49AED6;
            font-size: 25px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 5px;
            color: #FFF;
        }

        .table-top-right table {
            width: 100%;
        }

        .table-top-right table, .table-top-right table td {
            border: 1px solid #000;
        }

        .table-top-right table td {
            padding: 0 0.5rem;
        }

        .main-table table {
            width: 100%;
        }

        .main-table table,  .main-table table td {
            border: 1px solid #000;
            border-bottom: none;
        }

        .main-table table thead {
            text-align: center;
        }

        .main-table table td {
            text-align: center;
            padding: 0.5rem;
        }

        .last-info {
            margin-top: 10px;
            padding-right: 0;
        }

        table {
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            border-spacing: 0;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="title" style="width: 50%; display: inline-block;">
        {{ isset($work_order) ? 'Work Order' : 'Quote' }}
    </div>

        <div class="logo" style="width: 100%; text-align: right;">
        <div style="width:25%; margin-left: 75%; margin-bottom: 0">
            <span class="big-logo" style="display: block;">Cluttons</span>
        </div>
    </div>

        <div style="width: 100%; height: 100px; margin-top: 1rem;">
            <div style="width: 50%; display: inline-block;  margin-bottom: 0; padding-bottom: 0">
                <p style="margin-bottom: 0; padding-bottom: 0; margin-top: 1rem">
                    <span style="text-transform: uppercase; font-weight: bolder;">Supplier: {{$quote['supplier']['name']}}</span><br>
                    {{$quote['supplier']['phone']}}<br>
                    {{$quote['supplier']['email']}}
                </p>
            </div>
            <div class="table-top-right" style="width: 50%; display: inline-block;">
                <table style=" padding:0; margin-bottom: 0;">
                    <tr>
                        <td style="background-color: #49AED6; color: #FFF;text-transform: uppercase;">Date</td>
                        <td style="text-align: right">{{\Carbon\Carbon::parse($quote['due_at'])->format('d/m/Y')}}</td>
                    </tr>
                    <tr>
                        <td style="background-color: #49AED6; color: #FFF;text-transform: uppercase;">Order number</td>
                        <td style="text-align: right">{{$quote['id']}}</td>
                    </tr>
                    <tr>
                        <td style="background-color: #49AED6; color: #FFF;text-transform: uppercase;">Property Ref.</td>
                        <td style="text-align: right">{{$quote['property']['yardi_property_ref']}}</td>
                    </tr>
                    <tr>
                        <td style="background-color: #49AED6; color: #FFF;text-transform: uppercase;">Supplier A/C Number</td>
                        <td style="text-align: right">N/A</td>
                    </tr>
                    <tr>
                        <td style="background-color: #49AED6; color: #FFF;text-transform: uppercase;">Completion Date</td>
                        <td style="text-align: right"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="width: 100%; margin-left: 50%; padding-left: 2px; margin-bottom: 2rem;">
            <p>
                @if($quote['unit'])
                    <span style="font-weight: bolder; text-transform: uppercase; color: #333;">Contact:</span> {{$quote['unit']['property_manager']['user']['first_name']}} {{$quote['unit']['property_manager']['user']['last_name']}}
                    <br/>({{$quote['unit']['property_manager']['user']['email']}})
                @else
                    <span style="font-weight: bolder; text-transform: uppercase; color: #333;">Contact:</span> {{$quote['property']['property_manager']['user']['first_name']}} {{$quote['property']['property_manager']['user']['last_name']}}
                    <br/>({{$quote['property']['property_manager']['user']['email']}})
                @endif
            </p>
        </div>

        <div style="width: 50%; display: inline-block; margin-bottom: 0;">
            <span style="font-weight: bold; text-transform: uppercase; color: #333; margin-bottom: 20px;">Deliver To</span><br>
            <span style="white-space: pre-line;">{{$quote['property']['address']['lineFormatted']}}</span>
        </div>

        <div class="contact-top-right" style="width: 50%; display: inline-block">
                <span style="font-weight: bold; text-transform: uppercase; color: #333;">Invoice to</span><br>
                GMT Srl c/o Cluttons LLP<br>
                Portman House, 2, Portman Street<br>
                London, W1H 6DU
        </div>

        <div class="main-table" style="width:100%; margin-top: 0; padding-top: 0;">
            <table style="margin:0; border-left: none;">
                <tr style="text-align: center; text-transform: uppercase; background-color: #49AED6; border: 1px solid; color: #FFF;">
                    <th style="width:15%; border-left:1px solid #000; padding:0.5rem;">Unit of Service</th>
                    <th style="width:20%; padding:0.5rem; border-left:1px solid #000;">Type</th>
                    <th style="width:45%; padding:0.5rem; border-left:1px solid #000;">Instruction</th>
                    <th style="width:20%; padding:0.5rem; border-left:1px solid #000;">Value</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td style="text-transform: uppercase;">{{$quote['expenditure_type']['name']}} <strong>[{{$quote['expenditure_type']['code']}}]</strong></td>
                    <td>
                        <p>{{$quote['work_description']}}</p>
                        @unless(isset($work_order))
                        <span style="text-transform: uppercase; color:red; margin-top: 4rem;">Quotations required prior to works commencing</span>
                        @endunless
                    </td>
                    @unless(isset($work_order))
                        <td>{{ $quote['value'] ? "£ " . number_format($quote['value'], 2) : '-' }}</td>
                    @else
                    <td>{{ $work_order['value'] ? "£ " . number_format($work_order['value'], 2) : '-' }}</td>
                    @endunless
                </tr>
                <tr>
                    <td colspan="3" style="text-transform: uppercase; text-align: left;">
                        {{--<p><span style="font-weight: bold; text-transform: uppercase;">Contact Numbers:</span>&nbsp;&nbsp;&nbsp;--}}
                            {{--<span style="font-weight: bold; text-transform: uppercase;">Porter</span>&nbsp;&nbsp;property['porter_number']}}--}}
                            {{--&nbsp;&nbsp;&nbsp;<span style="font-weight: bold; text-transform: uppercase;">Tenant</span>&nbsp;&nbsp;property['tenant_number']}}--}}
                        {{--</p>--}}
                        {{--<p>--}}
                            {{--<span style="font-weight: bolder">order['documents']}}</span> documents have been provided as accompaniments--}}
                            {{--to this works order. By accepting this instruction, you are acknowledging receipt and understanding of the--}}
                            {{--contents of these documents.--}}
                        {{--</p>--}}
                        <strong>Contact Details</strong><br/>
                        <p>{{$quote['contact_details'] or '-'}}</p>
                    </td>
                    <td style="border-top:none;"></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-transform: uppercase; text-align: left;">
                        <strong>Critical Information</strong><br/>
                        {{$quote['critical_information'] or '-'}}
                    </td>
                    <td style="border-top: none;"></td>
                </tr>
                <tr style="border-left: none;">
                    <td colspan="3" style="text-transform: uppercase; text-align: right; border-left: none;">Total exc. vat</td>
                    @unless(isset($work_order))
                        <td style="border-bottom: 1px solid; font-weight: bold">{{ $quote['value'] ? "£ " . number_format($quote['value'], 2) : '-' }}</td>
                    @else
                    <td style="border-bottom: 1px solid; font-weight: bold">{{ $work_order['value'] ? "£ " . number_format($work_order['value'], 2) : '-' }}</td>
                    @endunless
                </tr>
            </table>
        </div>

        <div class="row last-info" style="width: 100%;">
            <div style="width:45%; display: inline-block; padding: 0;">
                If there is an existing written contract between us for the supply of goods and services, that will
                apply to this order. Otherwise, by accepting this order you also accept our standard terms and conditions,
                attached. These may only be varied if signed by one of our authorised representative.
            </div>
            <div style="width:13.5%; display: inline-block; padding: 0"> </div>
            <div style="text-align: right; display: inline-block; width:40%; padding: 0">
        <span style="font-weight: bold; text-transform: uppercase; color: #000;">
        Payment may be delayed if this purchase order is not attached to your invoice
        </span>
            </div>
        </div>
    </div>
</body>
</html>
