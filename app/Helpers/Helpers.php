<?php
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Crypt;

function setActive($path, $active = 'active')
{
    return call_user_func_array('Request::is', (array)$path) ? $active : '';
}
//function to Encrypt ID
function sst($id){
    return Crypt::encryptString($id);
}

//function to Decrypt ID
function tss($id){
    return Crypt::decryptString($id);
}


function generateSidebarLink($routeName, $icon, $title, $subRoutes) {
    $isActive = request()->routeIs($routeName) ? 'active' : 'collapsed';
    $isShow = request()->routeIs($routeName) ? 'show' : '';
    $html = "<a class='nav-link {$isActive}' href='javascript:void(0);' data-bs-toggle='collapse' data-bs-target='#collapse{$title}' aria-expanded='false' aria-controls='collapse{$title}'>
        <div class='nav-link-icon'><i data-feather='{$icon}'></i></div>
        {$title}
        <div class='sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
    </a>
    <div class='collapse {$isShow}' id='collapse{$title}' data-bs-parent='#accordionSidenav'>
        <nav class='sidenav-menu-nested nav accordion' id='accordionSidenavPages'>";
    foreach ($subRoutes as $subRoute) {
        $subRouteName = $subRoute['name'];
        $subRouteTitle = $subRoute['title'];
        $subRouteIsActive = request()->routeIs($subRouteName) ? 'active' : '';
        $html .= "<a class='nav-link {$subRouteIsActive}' href='" . route($subRouteName) . "'>{$subRouteTitle}</a>";
    }
    $html .= "</nav></div>";
    return $html;
}



function generateSidenavAccordion($routeName, $icon, $title, $subRoutes)
{
    $isActive = request()->is($routeName . '*') ? 'active' : 'collapsed';
    $isShow = request()->is($routeName . '*') ? 'show' : '';
    $html = "<a class='nav-link {$isActive}' href='javascript:void(0);' data-bs-toggle='collapse' data-bs-target='#collapse{$title}' aria-expanded='false' aria-controls='collapse{$title}'>
        <div class='nav-link-icon'><i data-feather='{$icon}'></i></div>
        {$title}
        <div class='sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
    </a>
    <div class='collapse {$isShow}' id='collapse{$title}' data-bs-parent='#accordionSidenav'>
        <nav class='sidenav-menu-nested nav accordion' id='accordionSidenavPages'>";
    foreach ($subRoutes as $subRoute) {
        // dd($subRoute['permission']);
        $subRouteName = $subRoute['name'];
        $subRouteTitle = $subRoute['title'];
        $subRoutePermission = isset($subRoute['permission']) ? $subRoute['permission']:'null';
        $subRouteIsActive = request()->routeIs($subRouteName) ? 'active' : '';
        if (auth()->user()->hasPermission($subRoutePermission)) {
            $html .= "<a class='nav-link {$subRouteIsActive}' href='" . route($subRouteName) . "'>{$subRouteTitle}</a>";
        }
    }
    if(isset($html)){
    $html .= "</nav></div>";
    return $html;
    }else{
        return '';
    }

}


function resourceRoutes($route){
    $resource = [
        'index' => $route . '.index',
        'create' => $route . '.create',
        'store' => $route . '.store',
        'show' => $route . '.show',
        'edit' => $route . '.edit',
        'update' => $route . '.update',
        'destroy' => $route . '.destroy',
    ];

    $routes = array_map(function($resource){
        return request()->routeIs($resource);
    }, $resource);

    return in_array(true, $routes);
}


function validateAndCleanPhoneNumber($phoneNumber)
{
    $phoneNumber = trim($phoneNumber);
    $phoneNumber = str_replace([' ', '-', '.', '+', '-', '(', ')'], '', $phoneNumber);
    $cleanedNumber = preg_replace('/\D/', '', $phoneNumber);

    if (!ctype_digit($cleanedNumber)) {
        return false;
    }
    $validLengths = [10, 15];
    $numberLength = strlen($cleanedNumber);

    if (!in_array($numberLength, $validLengths)) {
        return false;
    }
    return $cleanedNumber;
}

function validatePhoneNumber($number): String
{
    $phone_number = preg_replace("/[^0-9]/", '', $number);
    ;
    if (strlen(preg_replace('#[^0-9]#i', '', $phone_number)) < 7 || strlen(preg_replace('#[^0-9]#i', '', $phone_number)) > 14) {
        return FALSE;
    } else {

        $y = substr($phone_number, -9);
        $z = str_ireplace($y, '', $phone_number);
        $p = str_ireplace('+', '', $z);

        $x = array(
            93 => " Afghanistan",
            255 => " Tanzania (United Republic of)",
        );

        foreach ($x as $key => $value) {
            if ($p == $key) {
                $country_name = $value;
                $code = $key;
            } else {
                $country_name = ' Tanzania (United Republic of)';
                $code = '255';
            }
        }

        $valid_number = $code . $y;

        $valid = array($country_name, $valid_number);
        return $valid_number;
    }
}

if (!function_exists('getEducationLevelClass')) {
    /**
     * Get the CSS class name for an education level key.
     *
     * @param string $levelKey
     * @return string
     */
    function getEducationLevelClass($levelKey)
    {
        $educationLevels = [
            '1' => 'std-vii',
            '2' => 'form-4',
            '3' => 'form-vi',
            '4' => 'college',
            '5' => 'university',
            '6' => 'other',
        ];
        return strtoupper($educationLevels[$levelKey]) ?? '';
    }
}

if (!function_exists('getDurationClass')) {
    /**
     * Get the CSS class name for a duration key, excluding key '9'.
     *
     * @param string $durationKey
     * @return string
     */
    function getDurationClass($durationKey)
    {
        $durations = [
            '1' => '1-week',
            '2' => '2-week',
            '3' => '3-week',
            '4' => '4-week',
            '5' => '5-week',
            '6' => '6-week',
            '7' => '7-week',
            '8' => '8-week',
            '9' => '9-week',
            '10' => '10-week',
            '11' => '11-week',
            '12' => '12-week',
            '13' => '13-week',
            '14' => '14-week',
            '15' => '15-week',
            '16' => '16-week',
            '17' => '17-week',
            '18' => '18-week',
            '19' => '19-week',
            '20' => '20-week',
            '21' => '21-week',
            '22' => '22-week',
            '23' => '23-week',
            '24' => '24-week',
            '25' => '25-week',
        ];

        // Check if the key exists in the array, and return the corresponding class name
        if (isset($durations[$durationKey]) && $durationKey < '26') {
            return ucfirst($durations[$durationKey]);
        } else {
            return '';
        }
    }
}

if (!function_exists('getStatusBadge')) {
    /**
     * Get a badge HTML element based on the status value.
     *
     * @param int $status
     * @return string
     */
    function getStatusBadge($status)
    {
        switch ($status) {
            case 1:
                return '<span class=" badge bg-green-soft text-green">Paid</span>';
            case 2:
                return '<span class="badge bg-yellow-soft text-yellow">Partial Paid</span>';
            default:
                return '<span class="badge bg-red-soft text-red">Not Paid</span>';
        }
    }
}

function return_boolean_badge($status): string
{
    switch ($status){
        case 1:
            return '<span class=" badge bg-green">Yes</span>';
        default:
            return '<span class=" badge bg-red">No</span>';
    }
}

function new_sessions($status){

    if($status==false){
        return '<span class=" badge bg-green">New Session</span>';
    }else{
        return '<span class=" badge bg-red">Already Teached</span>';
    }
}

if (!function_exists('active_or_disabled')){
    function active_or_disabled($status):string
    {
        switch ($status){
            case 1:
                return '<span class=" badge bg-green">Yes</span>';
            case 0:
                return '<span class=" badge bg-red">Not yet</span>';
            default:
                return '<span class=" badge bg-secondary">disabled</span>';
        }
    }
}

if (!function_exists('active_or_inactive')) {
    function active_or_inactive($status): string
    {
        switch ($status) {
            case 1:
                return '<span class=" badge bg-green">Active</span>';
            case 0:
                return '<span class=" badge bg-red">Not Active</span>';
            default:
                return '<span class=" badge bg-secondary">disabled</span>';
        }
    }
}

if (!function_exists('getPaymentStatus')) {
    /**
     * Get a badge HTML element based on the status value.
     *
     * @param int $status
     * @return string
     */
    function getPaymentStatus($status)
    {
        switch ($status) {
            case 0:
                return '<span class="badge bg-green-soft text-green">Paid</span>';
            case 1:
                return '<span class="badge bg-yellow-soft text-yellow">Partial Paid</span>';
            default:
                return '<span class="badge bg-red-soft text-red">Not Paid</span>';
        }
    }
}


function trimValues(array $data): array
{
    return array_map('trim', $data);
}


// function imageEngine(array|\Illuminate\Http\UploadedFile|null $image, array $validated, float $width=null , float $height=null): array
// {
//     $extension = $image->getClientOriginalExtension();
//     $filename = time() . '.' . $extension;
//     if ($extension != "svg") {
//         $image_resize = Image::make($image->getRealPath());
//         if ($height == null) {
//             $image_resize->resize($width, null, function ($constraint) {
//                 $constraint->aspectRatio();
//             });
//         }elseif($width == null){
//             $image_resize->resize(null, $height, function ($constraint) {
//                 $constraint->aspectRatio();
//             });
//         }
//         else {
//             $image_resize->resize($width, $height);
//         }
//         $image_resize->encode($extension, 100);
//         $image_resize->save(storage_path('app/public/' . $filename));
//     } else {
//         $image->storeAs('public', $filename);
//     }
//     $validated['image'] = $filename;
//     return $validated;
// }

function imageEngine($image, $filename = null, $width = null, $height = null,$path=null)
{

    $filename = $filename ? $filename : time() . '.' . 'jpg';

    $image_resize = Image::make($image->getRealPath());

    if ($width == null && $height == null) {
        if ($image->width() > $image->height()) {
            $image_resize->resize(920, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $image_resize->resize(null, 540, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
    } elseif ($height == null) {
        $image_resize->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });
    } elseif ($width == null) {
        $image_resize->resize(null, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    } else {
        $image_resize->resize($width, $height);
    }

    $path = public_path("storage/$path");
    if (!File::isDirectory($path)) {
        File::makeDirectory($path, 0755, true);
    }

    $quality = 80;
    do {
        $image_resize->encode('jpg', $quality);
        $image_resize->save($path . $filename);
        clearstatcache();

        if (file_exists($path . $filename) && is_readable($path . $filename)) {
            $fileSize = filesize($path . $filename) / 1024;


            if ($fileSize > 100) {
                if ($quality - 10 < 0) {
                    break;
                }
                try {
                    $quality -= 10;
                    $image_resize->encode('jpg', $quality);
                } catch (InvalidArgumentException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        } else {
            echo "Error: File does not exist or is not readable.";
            break;
        }
    } while ($fileSize > 100);
}

function deleteImage($image, $path = null)
{
    $path = public_path("storage/$path");
    if (File::exists($path . $image)) {
        File::delete($path . $image);
    }
}



use App\Mail\SendMail;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserPermission;

if (!function_exists('sendEmail')) {
    function sendEmail($to, $name, $subject, $message)
    {
        $data = array(
            'to' => $to,
            'name' => $name,
            'subject' => $subject,
            'message' => $message
        );

        Mail::send(new SendMail($data));
    }
}

if(!function_exists('user_name')){
    function user_name($id){
        $user = \App\Models\User::find($id);
        return ucwords($user->name);
    }
}

if(!function_exists('applicant_name')){
    function applicant_name($id){
        if($id == null){
            return 'N/A';
        }else{
            $applicant = \App\Models\Applicant::find($id);
            if($applicant == null){
                return $id;
            }else{
                return ucwords($applicant->full_name);
            }
        }
    }
}


function generateInvoicePrefix()
{
    $numericPart = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);

    $capitalLetters = '';
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for ($i = 0; $i < 3; $i++) {
        $capitalLetters .= $characters[rand(0, strlen($characters) - 1)];
    }
    $mixedPart = '';
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    for ($i = 0; $i < 3; $i++) {
        $mixedPart .= $characters[rand(0, strlen($characters) - 1)];
    }
    $prefix = 'OUT' . $numericPart . $capitalLetters . $mixedPart;
    return $prefix;
}


function getSiteSettings()
{
    $siteSettings = \App\Models\Setting::first()
        ->only([
            'system_name',
            'system_logo',
            'allow_dark_sidebar',
            'allow_message',
            'allow_email',
            'enable_upload_receipt',
            // 'mail_mailer',
            // 'mail_host',
            // 'mail_port',
            // 'mail_username',
            // 'mail_password',
            // 'mail_encryption',
            // 'mail_from_address',
            'site_description',
            'site_contact_email',
            'site_contact_number',
        ]);
    return $siteSettings;
}

use SimpleSoftwareIO\QrCode\Facades\QrCode;

function encrypt_code($number)
{
    $replace = ['A', 'e_', 'jk', 'F{', 'rgc', 'Db', 'm$', 'Z-', 'd', 'xY'];
    $key = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0];

    $number = str_replace($key, $replace, $number);
    return $number;
}
function decrypt_code($number)
{
    $replace = ['A', 'e_', 'jk', 'F{', 'rgc', 'Db', 'm$', 'Z-', 'd', 'xY'];
    $key = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0];

    $number = str_replace($replace, $key, $number);
    return $number;
}

function qrcode($id, $format)
{
    $link = 'ViewInvoice';
    $code = url('/ViewInvoice/' . $id);
    $qrCode = QrCode::size(1000)->format('png')->generate($code);

    // Save the QR code image to a file
    $filePath = public_path('qrcodes/' . 'qrcode-'.$id . '.png');
    file_put_contents($filePath, $qrCode);


    if ($format == 'pdf') {
        $output = '<img src="' . $filePath . '">';
    } else {
        $output = $qrCode;
    }
    return $output;
}


function removeComma($dt)
{
    return (float) str_replace(',', '', $dt);
}

function number_to_words($number)
{
    if (($number < 0) || ($number > 999999999)) {
        return "$number";
    }

    $Gn = floor($number / 1000000); /* Millions (giga) */
    $number -= $Gn * 1000000;
    $kn = floor($number / 1000); /* Thousands (kilo) */
    $number -= $kn * 1000;
    $Hn = floor($number / 100); /* Hundreds (hecto) */
    $number -= $Hn * 100;
    $Dn = floor($number / 10); /* Tens (deca) */
    $n = $number % 10; /* Ones */

    $res = "";

    if ($Gn) {
        $res .= number_to_words($Gn) . " Million";
    }

    if ($kn) {
        $res .= (empty($res) ? "" : " ") .
            number_to_words($kn) . " Thousand";
    }

    if ($Hn) {
        $res .= (empty($res) ? "" : " ") .
            number_to_words($Hn) . " Hundred";
    }

    $ones = array(
        "",
        "One",
        "Two",
        "Three",
        "Four",
        "Five",
        "Six",
        "Seven",
        "Eight",
        "Nine",
        "Ten",
        "Eleven",
        "Twelve",
        "Thirteen",
        "Fourteen",
        "Fifteen",
        "Sixteen",
        "Seventeen",
        "Eightteen",
        "Nineteen"
    );
    $tens = array(
        "",
        "",
        "Twenty",
        "Thirty",
        "Fourty",
        "Fifty",
        "Sixty",
        "Seventy",
        "Eigthy",
        "Ninety"
    );

    if ($Dn || $n) {
        if (!empty($res)) {
            $res .= " and ";
        }

        if ($Dn < 2) {
            $res .= $ones[$Dn * 10 + $n];
        } else {
            $res .= $tens[$Dn];

            if ($n) {
                $res .= "-" . $ones[$n];
            }
        }
    }

    if (empty($res)) {
        $res = "zero";
    }

    return $res;
}

function allocatePermission($role,$user=null, $permission=null){
    $users = User::where('davinci_role_id',$role)->get();
    $permissions = RolePermission::where('role_id',$role)->get();
    if($permission && $user){
            UserPermission::updateOrCreate([
                'user_id'=>$user,
                'permission_id'=>$permission,
                'granted'=>'yes'
            ]);
    }elseif($user){
        foreach($permissions as $value){
                UserPermission::updateOrCreate([
                    'user_id'=>$user,
                    'permission_id'=>$value->permission_id,
                    'granted'=>'yes'
                ]);
        }

    }else{
        foreach($permissions as $value){
            foreach($users as $user){
                UserPermission::updateOrCreate([
                    'user_id'=>$user->id,
                    'permission_id'=>$value->permission_id,
                    'granted'=>'yes'
                ]);
            }
        }
    }
}

function deallocatePermission($role,$user=null, $permission=null){
    $permissions = RolePermission::where('role_id',$role)->get(['permission_id']);

    if($user && $permission){
        UserPermission::where('user_id',$user)->where('permission_id',$permission)->delete();
    }elseif($user){
        UserPermission::where('user_id',$user)->delete();
    }else{
        UserPermission::whereIn('user_id',User::where('davinci_role_id',$role)->get(['id']))->whereIn('permission_id',$permissions)->delete();
    }
}

function resourceRoutesCopy($route){
    $resource = [
        'manage' => $route . '.manage',
        'logs' => $route . '.logs',
    ];

    $routes = array_map(function($resource){
        return request()->routeIs($resource);
    }, $resource);

    return in_array(true, $routes);
}
