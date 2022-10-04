<?php


Route::group(['prefix' => 'invitations', 'namespace' => '\App\OurEdu\Invitations\Controllers\Api'], function () {
    Route::post('/{id}/change-status', 'InvitationController@changeStatus')
        ->name('invitations.changeStatus');

    Route::get('search', 'InvitationController@search')
        ->name('invitations.search');

    Route::post('invite', 'InvitationController@invite')
        ->name('invitations.invite');

    Route::post('/{id}/resend-invite', 'InvitationController@resendInvite')
        ->name('invitations.resendInvite');

    Route::post('{id}/cancel', 'InvitationController@cancelInviation')
        ->name('invitations.cancelInviation');
});
