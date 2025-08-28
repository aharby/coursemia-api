<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PermissionsEnum extends Enum
{
    // User & Platform
    const MANAGE_USERS = 'manage-users';
    const MANAGE_ROLES = 'manage-roles';
    const MANAGE_SETTINGS = 'platform-settings';
    const VIEW_ANALYTICS = 'view-analytics';
    const MANAGE_PAYMENTS = 'manage-payments';

    // Courses
    const CREATE_COURSE = 'create-course';
    const EDIT_COURSE = 'edit-course';
    const DELETE_COURSE = 'delete-course';
    const VIEW_COURSE = 'view-course';

    // Course Content
    const VIEW_COURSE_CONTENT = 'view-course-content';
    const DELETE_COURSE_CONTENT = 'delete-course-content';
    const CREATE_COURSE_CONTENT = 'create-course-content';
    const EDIT_COURSE_CONTENT = 'edit-course-content';
    
    // Engagement
    const LEAVE_REVIEW = 'leave-review';
    const REPLY_REVIEWS = 'reply-reviews';
    const MANAGE_DISCUSSIONS = 'manage-discussions';

    // Enrollment
    const ENROLL_COURSE = 'enroll-course';
}
