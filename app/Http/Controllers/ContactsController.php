<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactListResource;
use App\Http\Resources\GenericContactListResource;
use App\Models\ConversationMessage;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class ContactsController extends Controller
{
    public function getContacts()
    {
        // 1. Identify org ID and timezone
        $organizationId = auth()->user()->organizationSettings->id;
        $timezone       = auth()->user()->organizationSettings->timezone;

        // Convert local "start of today" to UTC
        $startOfToday = Carbon::now($timezone)
            ->startOfDay()
            ->timezone('UTC')
            ->toDateTimeString();


        /**
         * -------------------------------------------------------------
         * 1) RECENT CONVERSATION ($recent_conversation)
         *    - All Customers that have a message on/after 12:00 AM (user’s timezone).
         *    - Sort by newest message first.
         * -------------------------------------------------------------
         */

        $recent_conversation = Customer::select(
                'customers.*',
                DB::raw('MAX(conversation_messages.created_at) as latest_message')
            )
            ->where('customers.organization_id', $organizationId)
            ->leftjoin('conversations', 'customers.id', '=', 'conversations.customer_id')
            ->leftjoin('conversation_messages', 'conversations.id', '=', 'conversation_messages.conversation_id')
            ->where('conversation_messages.created_at', '>=', $startOfToday)
            //->groupBy('customers.id')
            //->orderBy('conversation_messages.created_at', 'desc')
            // Since we are using aggregate

            ->groupBy('customers.id')
            ->orderBy('latest_message', 'desc')
            ->get();



        /**
         * -------------------------------------------------------------
         * 2) PREVIOUS CONVERSATION ($previous_conversation)
         *    - All Customers with messages before 12:00 AM (user’s timezone).
         *    - Sort by newest message first.
         * -------------------------------------------------------------
         */



        $previous_conversation = Customer::select(
            'customers.*',
            DB::raw('MAX(conversation_messages.created_at) as latest_message')
        )
            ->where('customers.organization_id', $organizationId)
            ->leftjoin('conversations', 'customers.id', '=', 'conversations.customer_id')
            ->leftjoin('conversation_messages', 'conversations.id', '=', 'conversation_messages.conversation_id')
            ->where('conversation_messages.created_at', '<', $startOfToday)
            //->groupBy('customers.id')
            //->orderBy('conversation_messages.created_at', 'desc')
            // Since we are using aggregate

            ->groupBy('customers.id')
            ->orderBy('latest_message', 'desc')
            ->get();

        /**
         * -------------------------------------------------------------
         * 3) ASSIGNED ($assigned_conversation)
         *    - All Customers assigned to current user.
         *    - Ordered by an 'assigned_at' column descending.
         * -------------------------------------------------------------
         */
        $assigned_conversation = Customer::where('organization_id', $organizationId)
            ->where('assigned_to_id', auth()->id())   // or however you track assignments
            ->where('status', "0")
            ->orderBy('assigned_at', 'desc')
            ->get();

        /**
         * ----------------------------`---------------------------------
         * 4) NEW CUSTOMERS CREATED TODAY ($new_today)
         *    - All customers in the org, created after 12:00 AM local.
         *    - No eager loading. Order by created_at desc.
         * -------------------------------------------------------------
         */
        $new_today = Customer::where('organization_id', $organizationId)
            ->where('created_at', '>=', $startOfToday)
            ->orderBy('created_at', 'desc')
            ->get();

        // Return in your desired format
        return response()->json([
            "data" => [
                'recent'    => ContactListResource::collection($recent_conversation),
                'previous'  => ContactListResource::collection($previous_conversation),
                'assigned'  => GenericContactListResource::collection($assigned_conversation),
                'new_today' => GenericContactListResource::collection($new_today),
            ]
        ]);
    }
}
