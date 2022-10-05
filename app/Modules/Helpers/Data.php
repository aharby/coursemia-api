<?php

////////////// Default Data
function insertDefaultTokens()
{
    $rows = [
    ];
    if ($rows) {
        \DB::table('tokens')->insert($rows);
    }
}

function insertDefaultConfigs()
{
    \Cache::forget('configs');
    @copy(public_path() . '/img/logo.png', storage_path() . '/app/public/uploads/small/logo.png');
    @copy(public_path() . '/img/logo.png', storage_path() . '/app/public/uploads/large/logo.png');
    $rows = [];
    //////////// $appName
    $appName = env('APP_NAME');
    $row = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Basic Information',
        'field' => 'application_name',
        'label:en' => 'Ta3lom',
        'label:ar' => 'تعلم',
        'value' => $appName,
        'created_by' => 2,
    ];
    $rows[] = $row;

    ///////////////// Logo
    $rows[] = [
        'field_type' => 'file',
        'field_class' => 'custom-file-input',
        'type' => 'Basic Information',
        'field' => 'logo',
        'label:en' => 'Logo',
        'label:ar' => 'Logo',
        'value' => 'logo.png',
        'created_by' => 2,
    ];
    ///////////////// Email
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'email',
        'label:en' => 'Email',
        'label:ar' => 'Email',
        'value' => env('CONTACT_EMAIL', 'info@ta3lom.com'),
        'created_by' => 2,
    ];
    ///////////////// Phone
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'phone',
        'label:en' => 'تيلفون',
        'label:ar' => 'Phone',
        'value' => '12345678',
        'created_by' => 2,
    ];
    ///////////////// Mobile
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'mobile',
        'label:en' => 'Mobile',
        'label:ar' => 'Mobile',
        'value' => '12345678',
        'created_by' => 2,
    ];
    ///////////////// Facebook
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'facebook_url',
        'label:en' => 'Facebook',
        'label:ar' => 'Facebook',
        'value' => 'www.facebook.com',
        'created_by' => 2,
    ];
    ///////////////// Twitter
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'twitter_url',
        'label:en' => 'Twitter',
        'label:ar' => 'Twitter',
        'value' => 'www.twitter.com',
        'created_by' => 2,
    ];
    ///////////////// YouTube
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'youtube_url',
        'label:en' => 'YouTube',
        'label:ar' => 'YouTube',
        'value' => 'www.youtube.com',
        'created_by' => 2,
    ];
    ///////////////// LinkedIn
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'linkedin_url',
        'label:en' => 'LinkedIn',
        'label:ar' => 'LinkedIn',
        'value' => 'www.linkedin.com',
        'created_by' => 2,
    ];
    ///////////////// longitude
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'longitude',
        'label:en' => 'Location (longitude)',
        'label:ar' => 'الموقع (longitude)',
        'value' => '31.324104799999986',
        'created_by' => 2,
    ];
    ///////////////// latitude
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'latitude',
        'label:en' => 'Location (latitude)',
        'label:ar' => 'الموقع (latitude)',
        'value' => '30.0685382',
        'created_by' => 2,
    ];


    foreach ($rows as $row) {
        \App\Modules\Config\Config::create($row);
    }
}

//function insertDefaultOptions()
//{
//    $rows = [];
//    $rows[] = [
//        'title:en' => 'Academic Year',
//        'title:ar' => 'Academic Year',
//        'is_active' => 0,
//        'type' => 'academic_year',
//    ];
//    $rows[] = [
//        'title:en' => 'educational term',
//        'title:ar' => 'educational term',
//        'is_active' => 0,
//        'type' => 'educational_term',
//    ];
//    foreach ($rows as $row) {
//        \App\Modules\Options\Option::create($row);
//    }
//}
