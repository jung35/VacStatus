<?php

class SubscriptionController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /subscription
     *
     * @return Response
     */
    public function index()
    {
        return "test";
        //
    }

    /**
     * Store a newly created resource in storage.
     * POST /subscription
     *
     * @return Response
     */
    public function store()
    {
        $list_id = Input::get('list_id');

        $userList = UserList::find($list_id)->first();

        if($userList->null) {
            return Redirect::back()->with('error', 'Could not find list.');
        }

        if(!$userList->canSubscribe(Auth::User()->id)) {
            return Redirect::back()->with('error', 'You have no permission to this list.');
        }

        $subscription = new Subscription;
        $subscription->user_id = Auth::User()->id;
        $subscription->user_list_id = $list_id;

        if($subscription->save()) {
            return Redirect::back()->with('success', 'You have successfully subscribed to the list.');
        }

        return Redirect::back()->with('error', 'There was an error trying to subscribe to the list.');
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /subscription/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if(Subscription::whereUserId(Auth::User()->id)->whereUserListId($id)->first()->delete()) {
            return Redirect::back()->with('success', 'You have successfully un-subscribed to the list.');
        }

        return Redirect::back()->with('error', 'There was an error trying to unsubscribe from list.');
    }

}
