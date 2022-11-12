<?php

////////////// Default Data
use App\Modules\Config\Config;

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
    $factory = \Faker\Factory::create();
    $rows = [];
    //////////// $appName
    $appName = env('APP_NAME');
    $row = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Basic Information',
        'field' => 'application_name',
        'label:en' => 'Application Name',
        'label:ar' => 'اسم التطبيق',
        'value:en' => $appName,
        'value:ar' => $appName,
        'created_by' => 2,
    ];
    $rows[] = $row;

    ///////////////// Email
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'email',
        'label:en' => 'Email',
        'label:ar' => 'بريد الكترونى',
        'value:en' => env('CONTACT_EMAIL', 'mail@gamil.com'),
        'value:ar' => env('CONTACT_EMAIL', 'mail@gamil.com'),
        'created_by' => 2,
    ];
    ///////////////// Phone
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'phone',
        'label:en' => 'Phone',
        'label:ar' => 'تيلفون',
        'value:en' => '12345678',
        'value:ar' => '12345678',
        'created_by' => 2,
    ];
    ///////////////// Facebook
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'facebook_url',
        'label:en' => 'Facebook',
        'label:ar' => 'رابط الفيسبوك',
        'value:en' => 'www.facebook.com',
        'value:ar' => 'www.facebook.com',
        'created_by' => 2,
    ];
    ///////////////// Twitter
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'twitter_url',
        'label:en' => 'Twitter',
        'label:ar' => 'رابط تويتر',
        'value:en' => 'www.twitter.com',
        'value:ar' => 'www.twitter.com',
        'created_by' => 2,
    ];
    ///////////////// YouTube
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'youtube_url',
        'label:en' => 'YouTube',
        'label:ar' => 'رابط قناه اليوتيوب',
        'value:en' => 'www.youtube.com',
        'value:ar' => 'www.youtube.com',
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
        'value:en' => 'www.linkedin.com',
        'value:ar' => 'www.linkedin.com',
        'created_by' => 2,
    ];
    ///////////////// telegram
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'telegram_url',
        'label:en' => 'Telegram',
        'label:ar' => 'تيليجرام',
        'value:en' => 'www.telegram.com',
        'value:ar' => 'www.telegram.com',
        'created_by' => 2,
    ];
    ///////////////// Instagram
    $rows[] = [
        'field_type' => 'text',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'instagram_url',
        'label:en' => 'Instagram',
        'label:ar' => 'انستجرام',
        'value:en' => 'www.Instagram.com',
        'value:ar' => 'www.Instagram.com',
        'created_by' => 2,
    ];
    ///// About Us Text
    $rows[] = [
        'field_type' => 'text_editor',
        'field_class' => '',
        'type' => 'Contact Information',
        'field' => 'about_us_text',
        'label:en' => 'About Us Text',
        'label:ar' => 'نص صفحه عنا',
        'value:en' => $factory->realText(500),
        'value:ar' => $factory->realText(500),
        'created_by' => 2,
    ];
    ///////////////// Android Version
    $rows[] = [
        'field_type' => 'number',
        'field_class' => '',
        'type' => 'Android Version',
        'field' => 'android_version',
        'label:en' => 'Android Version',
        'label:ar' => 'رقم فيرجن الاندرويد',
        'value:en' => '1.1',
        'value:ar' => '1.1',
        'created_by' => 2,
    ];
    ///////////////// IOS Version
    $rows[] = [
        'field_type' => 'number',
        'field_class' => '',
        'type' => 'iOS Version',
        'field' => 'ios_version',
        'label:en' => 'iOS Version',
        'label:ar' => 'رقم فيرجن الآيفون',
        'value:en' => '1.1',
        'value:ar' => '1.1',
        'created_by' => 2,
    ];


    foreach ($rows as $row) {
        Config::create($row);
    }
}
