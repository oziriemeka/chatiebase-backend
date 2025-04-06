<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorStatus;
use App\Helpers\SuccessStatus;
use App\Http\Resources\ConversationMessageResource;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\DeletedConversationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ConversationController extends Controller
{
    public function getConversation($conversationId)
    {

        $organizationId = auth()->user()->organizationSettings->id;
        $conversation = Conversation::where("conversation_id", $conversationId)
            ->where("organization_id", $organizationId)->first();


        if(!$conversation){
            return response()->json([ErrorStatus::ERROR => [ErrorStatus::SYSTEM_ERROR => ['Invalid request, unable to proceed.']]], 404);
        }


        if($organizationId !== $conversation->customer->organization_id){
            return response(["error" => "Invalid request"], ErrorStatus::REQUEST_ERROR);
        }


        $messages = ConversationMessage::where('customer_id', $conversation->customer->id)->paginate(20);

        return response([
            'data' => [
                "id" => $conversation->conversation_id,
                'conversation' => ConversationMessageResource::collection($messages)
            ]
        ]);
    }

    public function deleteConversationMessage($conversationId, $messageId)
    {
        $organizationId = auth()->user()->organizationSettings->id;
        $conversation = Conversation::where("conversation_id", $conversationId)
            ->where("organization_id", $organizationId)->first();

        if(!$conversation){
            return response()->json([ErrorStatus::ERROR => [ErrorStatus::SYSTEM_ERROR => ['Invalid request, unable to proceed.']]], 404);
        }

        //Log deleted by
        // Also the below means anyone in organization can delete message
        $message = ConversationMessage::where("message_id", $messageId)->first();

        if(!$message){
            return response()->json([ErrorStatus::ERROR => [ErrorStatus::SYSTEM_ERROR => ['Invalid request, unable to proceed.']]], 422);
        }

        $message->is_deleted = true;
        $message->save();

        $logAction = new DeletedConversationMessage();
        $logAction->user_id = auth()->user()->id;
        $logAction->message_id = $message->id;
        $logAction->save();

        return response()->json([
            SuccessStatus::DATA => "Message deleted successfully"
        ]);
    }

    public function sendConversationMessage($conversationId, Request $request)
    {
        $rules = array(
            'message' => 'required|string|max:3000',
        );

        $messages = [
            'message.required' => '*  Message body can not be empty',
            'message.string' => '* Please enter a valid message',
            'message.max' => '* message is too long',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            $organizationId = auth()->user()->organizationSettings->id;
            $conversation = Conversation::where("conversation_id", $conversationId)
                ->where("organization_id", $organizationId)->first();

            if(!$conversation){
                return response()->json([ErrorStatus::ERROR => [ErrorStatus::SYSTEM_ERROR => ['Invalid request, unable to proceed.']]], 422);
            }

            try {
                // Todo : consider birthday Birthday paradox
                $message = new ConversationMessage();
                $message->message_id = Str::uuid()->toString();
                $message->conversation_id = $conversation->id;
                $message->customer_id = $conversation->customer_id;
                $message->user_id = auth()->user()->id;
                $message->sender = "operator";
                $message->action_type = "message";
                $message->message = $request->message;
                $message->save();

                return response()->json([
                    SuccessStatus::DATA => [
                        "id" => $message->id
                    ]
                ]);
            } catch (\Exception $ex){
                return response()->json([ErrorStatus::ERROR => [ErrorStatus::SYSTEM_ERROR => ['System error, unable to send message.']]], 422);
            }
        }
    }
}
