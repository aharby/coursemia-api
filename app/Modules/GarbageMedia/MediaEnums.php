<?php

namespace App\Modules\GarbageMedia;

class MediaEnums
{
    const SUPPORTED_TYPES = ['jpeg', 'png', 'jpg', 'gif', 'svg', 'bmp', 'pdf', 'xls', 'csv', 'txt', 'xlsx', 'mp4', 'webm', 'wmv', 'avi', 'flv', 'swf', 'mpga', 'audio', 'mpeg', 'doc', 'docx', 'mp3', 'one'];

    const VIDEO_TYPES = ['mp4', 'webm', 'wmv', 'avi', 'flv', 'mpeg', 'one'];
    const AUDIO_TYPES = ['mp3', 'audo', 'mpga', 'audio'];
    const IMAGE_TYPES = ['jpeg', 'png', 'jpg', 'gif', 'svg', 'bmp'];
    const PDF_TYPES = ['pdf'];
    const FLASH_TYPES = ['swf'];
    const DOCUMENT_TYPES = ['xls', 'csv', 'txt', 'xlsx', 'doc', 'docx'];
    const COURSES_MEDIA_TYPES = ['pdf', 'doc', 'docx', 'jpeg', 'png', 'jpg', 'gif', 'svg', 'bmp', 'mp4', 'webm', 'wmv', 'avi', 'flv', 'mpeg', 'one'];

    const VIDEO_TYPE = 'video';
    const AUDIO_TYPE = 'audio';
    const IMAGE_TYPE = 'image';
    const PDF_TYPE = 'pdf';
    const DOCUMENT_TYPE = 'document';


    const ICONS = [
        self::VIDEO_TYPE => "video",
        self::AUDIO_TYPE => "volume-up",
        self::IMAGE_TYPE => "image",
        self::PDF_TYPE => "file-pdf",
        self::DOCUMENT_TYPE => "file-alt",
    ];

    const TYPE_DISPLAY = [
        self::VIDEO_TYPE => 1,
        self::AUDIO_TYPE => 1,
        self::IMAGE_TYPE => 1,
        self::PDF_TYPE => 1,
        self::DOCUMENT_TYPE => 0,
    ];

    public static function getTypeExtensions($type = null)
    {
        $extensions = [
            self::VIDEO_TYPE => self::VIDEO_TYPES,
            self::AUDIO_TYPE => self::AUDIO_TYPES,
            self::IMAGE_TYPE => self::IMAGE_TYPES,
            self::PDF_TYPE => self::PDF_TYPES,
            self::DOCUMENT_TYPE => self::DOCUMENT_TYPES,
        ];

        return $type ? $extensions[$type] : $extensions;
    }

    public static function getTypeExtensionsIconDisplay($type = null, $params = null)
    {
        $extensionsPair = [
            self::VIDEO_TYPE => self::VIDEO_TYPES,
            self::AUDIO_TYPE => self::AUDIO_TYPES,
            self::IMAGE_TYPE => self::IMAGE_TYPES,
            self::PDF_TYPE => self::PDF_TYPES,
            self::DOCUMENT_TYPE => self::DOCUMENT_TYPES,
        ];
        foreach ($extensionsPair as $key => $extensions) {
            if (in_array($type, $extensions) && $params) {
                return ['extension' => $type, 'icon' => self::ICONS[$key], 'is_display' => self::TYPE_DISPLAY[$key]];
            }

            if (in_array($type, $extensions)) {
                return ['extension' => $key, 'icon' => self::ICONS[$key], 'is_display' => self::TYPE_DISPLAY[$key]];
            }
        }
        return [
            'extension_type' => self::DOCUMENT_TYPE,
            'icon' => self::ICONS[self::DOCUMENT_TYPE],
            'is_display' => self::TYPE_DISPLAY[self::DOCUMENT_TYPE]
        ];

    }

    public static function getMediaTypes()
    {
        return [
            self::AUDIO_TYPE,
            self::VIDEO_TYPE,
            self::IMAGE_TYPE,
            self::PDF_TYPE,
            self::DOCUMENT_TYPE,
        ];
    }

    public static function getCourseMediaTypes()
    {
        $coursesMediatypes = [];
        foreach (self::COURSES_MEDIA_TYPES as $type) {
            $coursesMediatypes[$type] = $type;
        }
        return $coursesMediatypes;
    }
}
