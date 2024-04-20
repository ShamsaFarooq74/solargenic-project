<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
</head>

<style>
    .normal{
        margin: 0;
        font-size: 12px !important;
        text-align: center;
    }
    p{
        margin: 0;
        font-size: 12px !important;
        text-align: center;
    }
    #dlComments p{
        margin: 0;
        font-size: 12px !important;
        text-align: left;
    }
</style>
<body>


<div align="center">
    <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100.0%">
        <tbody>
        <tr>
            <td style="padding:0in 0in 0in 0in">
                <div align="center">
                    <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0"
                           style="border-collapse:collapse">
                        <tbody>
                        <tr>
                            <td style="padding:.75pt .75pt .75pt .75pt">
                                <div align="center">
                                    <table class="MsoNormalTable" border="0" cellpadding="0" width="0"
                                           style="width:510.0pt">
                                        <tbody>
                                        <tr>
                                            <td width="93"
                                                style="width:.3pt; padding:.75pt .75pt .75pt .75pt">
                                                <p class="MsoNormal"><img
                                                        src="http://192.168.1.250/bel/assets/images/bel_logo.png"
                                                        width="88" height="74" id="Picture_x0020_4"
                                                        style="width: 0.9166in; height: 0.7708in; display: inline;">
                                                </p>
                                            </td>
                                            <td
                                                style="background:white; padding:.75pt .75pt .75pt .75pt">
                                                <p class="contactname" align="center"
                                                   style="text-align:center"><u><span
                                                            style="font-size:16pt; color:windowtext;font-family: 'Roboto', sans-serif; font-weight: bold;">New
                                                                            Ticket</span></u></p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:.75pt .75pt .75pt .75pt">
                                <div align="center">
                                    <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0"
                                           width="0" style="width:502.5pt; border-collapse:collapse">
                                        <tbody>
                                        <tr>
                                            <td width="101" nowrap=""
                                                style="font-weight: bold; font-family: 'Roboto', sans-serif;width:75.45pt; border:solid black 1px; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><b>TICKET ID</b></p>
                                            </td>
                                            <td width="99"
                                                style="width:74.4pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal" align="center"
                                                   style="text-align:center"><span
                                                        style="color:#1F497Dfont-family: 'Roboto', sans-serif;">{{ $data->ticket_id }}</span></p>
                                            </td>
                                            <td width="103" nowrap=""
                                                style="font-family: 'Roboto', sans-serif;width:77.0pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><b>PLANT NAME</b></p>
                                            </td>
                                            <td width="198"
                                                style="font-family: 'Roboto', sans-serif;width:148.25pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><span
                                                        style="color:#1F497D">{{$data->ticket_data->plant_name}}</span>
                                                </p>
                                            </td>
                                            <td width="54" nowrap=""
                                                style="font-weight: bold;font-family: 'Roboto', sans-serif;width:40.6pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="ini">DD/TIME</p>
                                            </td>
                                            <td width="116" nowrap=""
                                                style="font-family: 'Roboto', sans-serif;width:128.8pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="dt" align="center" style="text-align:center">
                                                    14/10/21 10:40 AM</p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="MsoNormal"><span style="">&nbsp;</span></p>
                                <div align="center">
                                    <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0"
                                           width="0" style="width:502.5pt; border-collapse:collapse">
                                        <tbody>
                                        <tr>
                                            <td width="101"
                                                style="font-family: 'Roboto', sans-serif;width:75.4pt; border:solid black 1px; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><b><span
                                                            style="color:#1F497D">Alternate Contact
                                                                            #</span></b></p>
                                            </td>
                                            <td width="99"
                                                style="font-size:10pt;text-align: center ;font-family: 'Roboto', sans-serif;width:74.55pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">{{isset($data->ticket_data->alternate_contact) ? $data->ticket_data->alternate_contact : "N/A"}}
                                            </td>
                                            <td width="77"
                                                style="font-family: 'Roboto', sans-serif;width:57.9pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><b>RECIEVER</b></p>
                                            </td>
                                            <td width="105"
                                                style="font-family: 'Roboto', sans-serif;width:78.95pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="MsoNormal"><span class="normal1"><span
                                                            style="font-size:8.0pt">PC
                                                                            DIAGNOSE</span></span> </p>
                                            </td>
                                            <td width="118"
                                                style="font-family: 'Roboto', sans-serif;width:88.55pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><b>TITLE</b></p>
                                            </td>
                                            <td width="170"
                                                style="font-family: 'Roboto', sans-serif;width:127.15pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="MsoNormal"><span class="normal1"><span
                                                            style="font-size:8.0pt">{{isset($data->ticket_data->title) ? $data->ticket_data->title : "N/A"}}</span></span>
                                                </p>
                                                {{--                                                <p class="MsoNormal"><span class="normal1"><span--}}
                                                {{--                                                            style="font-size:8.0pt">GFDGDFGDFGDSFGFDGSDFG</span></span>--}}
                                                {{--                                                </p>--}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="MsoNormal"><span style="">&nbsp;</span></p>
                                <div align="center">
                                    <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0"
                                           width="0" style="width:502.5pt; border-collapse:collapse">
                                        <tbody>
                                        <tr>
                                            <td width="101"
                                                style="font-weight: bold ;font-family: 'Roboto', sans-serif;width:64.5pt; border:solid black 1px; padding:.75pt .75pt .75pt .75pt">
                                                <p class="ini">DESCRIPTION</p>
                                            </td>
                                            <td width="282"
                                                style="font-family: 'Roboto', sans-serif;width:180.75pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal">{{isset($data->ticket_data->description) ? $data->ticket_data->description : "N/A"}} </p>
                                            </td>
                                            <td width="117"
                                                style="font-family: 'Roboto', sans-serif;width:75.0pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><b>ASSIGNED TO</b></p>
                                            </td>
                                            <td width="171"
                                                style="font-family: 'Roboto', sans-serif;width:109.5pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal">N/A </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="101"
                                                style="font-weight: bold ;font-family: 'Roboto', sans-serif;width:64.5pt; border:solid black 1px; border-top:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="ini">Alternate Email</p>
                                            </td>
                                            <td width="282"
                                                style="font-family: 'Roboto', sans-serif;width:180.75pt; border-top:none; border-left:none; border-bottom:solid black 1px; border-right:solid black 1px; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal">{{isset($data->ticket_data->alternate_email) ? $data->ticket_data->alternate_email : "N/A"}}&nbsp;</p>
                                            </td>
                                            <td width="117"
                                                style="font-family: 'Roboto', sans-serif;width:75.0pt; border-top:none; border-left:none; border-bottom:solid black 1px; border-right:solid black 1px; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><b>Due in </b></p>
                                            </td>
                                            <td width="171"
                                                style="font-family: 'Roboto', sans-serif;width:109.5pt; border-top:none; border-left:none; border-bottom:solid black 1px; border-right:solid black 1px; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal">&nbsp;N/A</p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="MsoNormal"><span style="">&nbsp;</span></p>
                                <div align="center">
                                    <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0"
                                           width="0" style="width:502.5pt; border-collapse:collapse">
                                        <tbody>
                                        <tr>
                                            <td width="101"
                                                style="font-weight: bold ;width:64.5pt; border:solid black 1px; padding:.75pt .75pt .75pt .75pt">
                                                <p class="ini">Location </p>
                                            </td>
                                            <td width="282"
                                                style="width:180.75pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><span class="normal1"><span
                                                            style="color:#1F497D">[CITY]</span> </span>
                                                </p>
                                            </td>
                                            <td width="117"
                                                style="width:75.0pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><b>STATUS</b></p>
                                            </td>
                                            <td width="171"
                                                style="font-weight: bold ;width:109.5pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal">{{isset($data->ticket_data->status) ? $data->ticket_data->status : "N/A"}}</p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="MsoNormal"><span style="">&nbsp;</span></p>
                                <div align="center">
                                    <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0"
                                           width="0" style="width:502.5pt; border-collapse:collapse">
                                        <tbody>
                                        <tr>
                                            <td width="101"
                                                style="width:64.5pt; border:solid black 1px; padding:.75pt .75pt .75pt .75pt">
                                                <p class="MsoNormal"><b><span
                                                            style="font-weight: bold ;font-size:7.5pt; font-weight: bold;font-family: 'Roboto', sans-serif;font-weight: bold;">Category
                                                                        </span></b></p>
                                            </td>
                                            <td width="282"
                                                style="width:180.75pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><span class="normal1">{{isset($data->ticket_data->category_name) ? $data->ticket_data->category_name : "N/A"}} </span></p>
                                            </td>
                                            <td width="117"
                                                style="width:75.0pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><b>SUB CATEGORY</b></p>
                                            </td>
                                            <td width="171"
                                                style="width:109.5pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal">{{isset($data->ticket_data->sub_category_name) ? $data->ticket_data->sub_category_name : "N/A"}} </p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="MsoNormal"><span style="">&nbsp;</span></p>
                                <div align="center">
                                    <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0"
                                           width="0" style="width:502.5pt; border-collapse:collapse">
                                        <tbody>
                                        <tr>
                                            <td width="101"
                                                style="width:64.5pt; border:solid black 1px; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><b><span
                                                            style="color:black;font-weight: bold;font-family: 'Roboto', sans-serif;font-weight: bold;">SOURCE</span> </b></p>
                                            </td>
                                            <td width="282"
                                                style="font-size:10pt;text-align: center ;font-family: 'Roboto', sans-serif;width:180.75pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                {{isset($data->ticket_data->name) ? $data->ticket_data->name : "N/A"}}
                                            </td>
                                            <td width="117"
                                                style="width:75.0pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="MsoNormal"><span class="normal1"><b><span
                                                                style="font-weight: bold; font-size:8.0pt; color:#1F497D">INVERTER
                                                                                SERIAL </span></b></span><span
                                                        class="normal1"><b><span
                                                                style="font-size:8.0pt">#</span></b></span>
                                                </p>
                                            </td>
                                            <td width="171"
                                                style="width:109.5pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">
                                                <p class="normal"><span style="color:#1F497D"> N/A</span> </p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="MsoNormal"><span style="">&nbsp;</span></p>
                                <div align="center">
                                    <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0"
                                           width="0" style="width:502.5pt; border-collapse:collapse">
                                        <tbody>
                                        <tr>
                                            <td width="101"
                                                style="font-weight: bold ;width:64.5pt; border:solid black 1px; padding:.75pt .75pt .75pt .75pt">
                                                <p class="MsoNormal" style=""><span
                                                        style="font-size:10.5pt; font-family:&quot;Roboto&quot;,serif; color:#504E4E">Current
                                                                        status<br>
                                                                        Plant Type<br>
                                                                        System Type<br>
                                                                        Capacity<br>
                                                                        Contact</span></p>
                                            </td>
                                            <td width="569" id="dlStatusReport_ctl01_td1"
                                                style="font-size:10pt;text-align: center ;font-family: 'Roboto', sans-serif;width:365.25pt; border:solid black 1px; border-left:none; padding:.75pt .75pt .75pt .75pt">Current
                                                {{isset($data->ticket_data->status) ? $data->ticket_data->status : "N/A"}}<br>
                                                {{isset($data->ticket_data->type) ? $data->ticket_data->type : "N/A"}}<br>
                                                {{isset($data->system_type->type) ? $data->system_type->type : "N/A"}}<br>
                                                {{isset($data->ticket_data->capacity) ? $data->ticket_data->capacity : "N/A"}}<br>
                                                {{isset($data->ticket_data->phone) ? $data->ticket_data->phone : "N/A"}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="MsoNormal" align="center" style="text-align:center"></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding:0in 0in 0in 0in">
                <div align="center">
                    <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0" width="0"
                           id="dlComments" style="width:510.0pt; border-collapse:collapse">
                        <tbody>
                        <tr>
                            <td style="padding:.75pt .75pt .75pt .75pt">
                                <p class="normal" align="center" style="text-align:center"><b>CHANGES
                                        HISTORY</b></p>
                                <div class="MsoNormal" align="center" style="text-align:center">
                                    <hr size="2" width="670" noshade="" align="center"
                                        style="width:502.5pt; color:black">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            @foreach($data->ticket_History as $key20 => $history )
                                <td style="padding:.75pt .75pt .75pt .75pt">
                                    <div align="center">
                                        <table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0"
                                               width="100%" style="width:100.0%">
                                            <tbody>
                                            <tr>
                                                <td width="70%" colspan="3"
                                                    style="width:70.0%; padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="normal"><b>Submit By : </b>{{$history->name}} </p>
                                                </td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td colspan="3" style="padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="normal"><strong><span
                                                                style="font-family: 'Roboto', sans-serif;">Dated
                                                                            :</span></strong> {{$history->created_at}} </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="normal"><strong><span
                                                                style="font-family: 'Roboto', sans-serif;">Note:
                                                                        </span></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        VIPER KEY BAORD AND MOUSE REPLACED AFTER THAT HANDED
                                                        OVER TO CUSTOMER </p>
                                                </td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                </td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="normal"><strong><span
                                                                style="font-family: 'Roboto', sans-serif;">Changes:</span></strong>
                                                    </p>
                                                </td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><strong><span
                                                                style="font-size:8.0pt; font-family: 'Roboto', sans-serif;">{{isset($history->description) ? $history->description : "N/A"}}</span></strong><b><span
                                                                style="font-size:8.0pt; font-family: 'Roboto', sans-serif;"><br>
                                                                            <strong><span
                                                                                    style="font-family: 'Roboto', sans-serif;">Status:{{isset($data->ticket_data->status) ? $data->ticket_data->status : "N/A"}}
                                                                                </span></strong></span></b><span
                                                            style="font-size:8.0pt; font-family: 'Roboto', sans-serif;"></span>
                                                    </p>
                                                </td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                </td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                                <td style="padding:1.5pt 1.5pt 1.5pt 1.5pt"></td>
                                            </tr>
                                            <tr>
                                                <td width="123"
                                                    style="width:92.25pt; padding:0in 0in 0in 0in"></td>
                                                <td width="342"
                                                    style="width:256.5pt; padding:0in 0in 0in 0in"></td>
                                                <td width="9" style="width:6.75pt; padding:0in 0in 0in 0in">
                                                </td>
                                                <td width="6" style="width:4.5pt; padding:0in 0in 0in 0in">
                                                </td>
                                                <td width="66"
                                                    style="width:49.5pt; padding:0in 0in 0in 0in"></td>
                                                <td width="66"
                                                    style="width:49.5pt; padding:0in 0in 0in 0in"></td>
                                                <td width="66"
                                                    style="width:49.5pt; padding:0in 0in 0in 0in"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="MsoNormal" align="center" style="text-align:center">
                                        <hr size="2" width="670" noshade="" align="center"
                                            style="width:502.5pt; color:black">
                                    </div>
                                </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
        <tr style="height:72.75pt">
            <td style="padding:0in 0in 0in 0in; height:72.75pt">
                <p align="center" style="text-align:center"><span
                        style="font-size:8.0pt; font-family: 'Roboto', sans-serif;">This is an
                                automatically generated email<span style="color:#1F497D"> BEL ENERGY</span> (Pvt.) Ltd.
                                will not be responsible for any Error.<br>
                                For further clarification or information regarding your problem, please reply and always
                                quote the ticket number in the subject field </p>
            </td>
        </tr>
        </tbody>
    </table>
</div>


</body>

</html>
