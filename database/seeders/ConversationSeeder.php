<?php

namespace Database\Seeders;

use App\Models\ApplicationChatSound;
use App\Models\Conversation;
use App\Models\ConversationAttachments;
use App\Models\ConversationCategory;
use App\Models\ConversationComment;
use App\Models\ConversationMessage;
use App\Models\ConversationTags;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\CustomerInteractionHistories;
use App\Models\WidgetSettings;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private  $conversationIdOne;
    private  $conversationIdTwo;
    private  $conversationIdThree;

    private  $conversationMessageIdOne;
    private  $conversationMessageIdTwo;
    private  $conversationMessageIdThree;
    public function __construct()
    {
       $this->conversationIdOne = Str::uuid()->toString();
       $this->conversationIdTwo = Str::uuid()->toString();
       $this->conversationIdThree = Str::uuid()->toString();

       $this->conversationMessageIdOne = Str::uuid()->toString();
       $this->conversationMessageIdTwo = Str::uuid()->toString();
       $this->conversationMessageIdThree = Str::uuid()->toString();

       $this->attachmentIdOne = Str::uuid()->toString();
       $this->attachmentIdTwo = Str::uuid()->toString();
       $this->attachmentIdThree = Str::uuid()->toString();
    }

    public function run(): void
    {
        $this->createRandomCustomer();
        $this->createRandomCustomerContactDetails();
        $this->createRandomConversationHistory();
        $this->createRandomAttachment();
        $this->createRandomConversation();
        $this->createRandomConversationMessage();
        $this->createConversationComment();
        $this->createDefaultConversationCategories();
        $this->createConversationTags();
    }

    private function createConversationTags()
    {

        $defaultCategories = [
            'client',
            'paid',
            'disturbing',
        ];

        foreach ($defaultCategories as $category) {
            ConversationTags::firstOrCreate(['name' => $category], ['conversation_id' => 1], ['organization_id' => 1]);
        }
    }

    private function createDefaultConversationCategories()
    {
        $defaultCategories = [
            'Completed',
            'Not our client',
            'Contact later',
            'Unsuccessful'
        ];

        foreach ($defaultCategories as $category) {
            ConversationCategory::firstOrCreate(['name' => $category], ['organization_id' => null]);
        }
    }

    private function createConversationComment(){
        for($i = 1; $i < 45; $i++) {
            $faker = Factory::create();
            $conversationComment = new ConversationComment();
            $conversationComment->conversation_id = $i;
            $conversationComment->user_id = 1;
            $conversationComment->comment = $faker->text(100);;
            $conversationComment->save();
        }
    }

    private function createRandomConversationMessage()
    {


        // $this->command->warn('here');
        for($i = 1; $i < 45; $i++) {
            $sender_id = random_int(0, 1);
            $faker = Factory::create();
            $conversationMessage = new ConversationMessage();
            $conversationMessage->message_id = Str::uuid()->toString();
            $conversationMessage->conversation_id = 1;
            $conversationMessage->customer_id = 1;
            $conversationMessage->user_id = 1;
            $conversationMessage->sender = $sender_id == 0 ? 'operator': 'customer';
            $conversationMessage->action_type = "message";
            $conversationMessage->message = $faker->text(100);
            $conversationMessage->created_at = Carbon::parse(now())->subDay();
            $conversationMessage->save();
        }

        unset($sender_id);

        $sender_id = random_int(0, 1);
        $faker = Factory::create();
        $conversationMessage = new ConversationMessage();
        $conversationMessage->message_id = $this->conversationMessageIdOne;
        $conversationMessage->attachment_id = 1;


        $conversationMessage->conversation_id = 1;
        $conversationMessage->customer_id = 1;
        $conversationMessage->user_id = 1;
        $conversationMessage->sender = $sender_id == 0 ? 'operator': 'customer';
        $conversationMessage->action_type = "message";
        $conversationMessage->message = $faker->text(100);
        $conversationMessage->created_at = Carbon::parse(now())->subDay();
        $conversationMessage->save();


        $sender_id = random_int(0, 1);
        $faker = Factory::create();
        $conversationMessage = new ConversationMessage();
        $conversationMessage->message_id = $this->conversationMessageIdTwo;
        $conversationMessage->conversation_id = 1;
        $conversationMessage->attachment_id = 2;

        $conversationMessage->customer_id = 1;
        $conversationMessage->user_id = 1;
        $conversationMessage->sender = $sender_id == 0 ? 'operator': 'customer';
        $conversationMessage->action_type = "message";
        $conversationMessage->message = $faker->text(100);
        $conversationMessage->created_at = Carbon::parse(now())->subDay();
        $conversationMessage->save();


        $sender_id = random_int(0, 1);
        $faker = Factory::create();
        $conversationMessage = new ConversationMessage();
        $conversationMessage->message_id = $this->conversationMessageIdThree;
        $conversationMessage->conversation_id = 1;
        $conversationMessage->attachment_id = 3;

        $conversationMessage->customer_id = 1;
        $conversationMessage->user_id = 1;
        $conversationMessage->sender = $sender_id == 0 ? 'operator': 'customer';
        $conversationMessage->action_type = "message";
        $conversationMessage->message = $faker->text(100);
        $conversationMessage->created_at = Carbon::parse(now())->subDay();
        $conversationMessage->save();


        unset($i);

        for($i = 1; $i < 45; $i++) {
            $sender_id = random_int(0, 1);
            $faker = Factory::create();
            $conversationMessage = new ConversationMessage();
            $conversationMessage->message_id = Str::uuid()->toString();
            $conversationMessage->conversation_id = 2;
            $conversationMessage->customer_id = 2;
            $conversationMessage->user_id = 1;
            $conversationMessage->sender = $sender_id == 0 ? 'operator': 'customer';
            $conversationMessage->action_type = "message";
            $conversationMessage->message = $faker->text(100);
            $conversationMessage->created_at = Carbon::parse(now())->subDay();
            $conversationMessage->save();
        }

        unset($i);


        for($i = 1; $i < 45; $i++) {
            $sender_id = random_int(0, 1);
            $faker = Factory::create();
            $conversationMessage = new ConversationMessage();
            $conversationMessage->message_id = Str::uuid()->toString();
            $conversationMessage->conversation_id = 3;
            $conversationMessage->customer_id = 3;
            $conversationMessage->user_id = 1;
            $conversationMessage->sender = $sender_id == 0 ? 'operator': 'customer';
            $conversationMessage->action_type = "message";
            $conversationMessage->message = $faker->text(100);
            $conversationMessage->created_at = Carbon::parse(now())->subDay();
            $conversationMessage->save();
        }

        unset($i);

        for($i = 4; $i < 45; $i++) {
            $sender_id = random_int(0, 1);
            $faker = Factory::create();
            $conversationMessage = new ConversationMessage();
            $conversationMessage->message_id = Str::uuid()->toString();
            $conversationMessage->conversation_id = $i;
            $conversationMessage->customer_id = $i;
            $conversationMessage->user_id = 1;
            $conversationMessage->sender = $sender_id == 0 ? 'operator': 'customer';
            $conversationMessage->action_type = "message";
            $conversationMessage->message = $faker->text(100);
            $conversationMessage->created_at = Carbon::parse(now())->subDay();
            $conversationMessage->save();
        }

        $this->command->warn('Last');

    }
    private function createRandomConversation()
    {
        $conversation = new Conversation();
        $conversation->customer_id = 1;
        $conversation->conversation_id =  $this->conversationIdOne;
        $conversation->user_id = 1;
        $conversation->organization_id = 1;
        $conversation->save();

        $conversation = new Conversation();
        $conversation->customer_id = 2;
        $conversation->conversation_id =  $this->conversationIdTwo;
        $conversation->user_id = 1;
        $conversation->organization_id = 1;
        $conversation->save();

        $conversation = new Conversation();
        $conversation->customer_id = 3;
        $conversation->conversation_id =  $this->conversationIdThree;
        $conversation->user_id = 1;
        $conversation->organization_id = 1;
        $conversation->save();

        for($i = 4; $i < 45; $i++){
            $conversation = new Conversation();
            $conversation->customer_id = $i;
            $conversation->conversation_id =  Str::uuid()->toString();
            $conversation->user_id = 1;
            $conversation->organization_id = 1;
            $conversation->save();
        }

    }
    private function createRandomAttachment()
    {
        $conversationAttachment = new ConversationAttachments();
        $conversationAttachment->attachement_id =  $this->attachmentIdOne;
        $conversationAttachment->message_id = $this->conversationMessageIdOne;
        $conversationAttachment->extension = "txt";
        $conversationAttachment->content = "document-one.txt";
        $conversationAttachment->size = "1200";  //1.2MB

        $conversationAttachment = new ConversationAttachments();
        $conversationAttachment->attachement_id =   $this->attachmentIdTwo;
        $conversationAttachment->message_id = $this->conversationMessageIdTwo;
        $conversationAttachment->extension = "png";
        $conversationAttachment->content = "image-one.png";
        $conversationAttachment->size = "1200";  //1.2MB

        $conversationAttachment = new ConversationAttachments();
        $conversationAttachment->attachement_id =   $this->attachmentIdThree;
        $conversationAttachment->message_id = $this->conversationMessageIdThree;
        $conversationAttachment->extension = "txt";
        $conversationAttachment->content = "image-two.jpg";
        $conversationAttachment->size = "1200";  //1.2MB
    }

    private function createRandomConversationHistory()
    {
        for($i = 1; $i < 45; $i++){
            $customerInteractionHistory = new CustomerInteractionHistories();
            $customerInteractionHistory->customer_id = $i;
            $customerInteractionHistory->last_page_visited = "https://xyz.com";
            $customerInteractionHistory->referral_header = "https://facebook";
            $customerInteractionHistory->save();
        }
    }
    private function createRandomCustomer()
    {
        // only 40 customer would have name, 4 won't

        for($i = 1; $i < 45; $i++){
            $faker = Factory::create();

            $customer = new Customer();
            if($i < 5) {
                $customer->assigned_by_id = 1;
                $customer->assigned_to_id = 1;
                $customer->assigned_at = now();
            }
            if($i < 40){
                $customer->name = $faker->name;
            }
            $customer->name = $faker->name;
            $customer->customer_id = Str::uuid()->toString();
            $customer->unique_identifier = Str::random(220);
            $customer->ip_address = $faker->ipv4;
            $customer->country = $faker->country;
            $customer->organization_id = 1;
            $customer->save();
        }
    }
    private function createRandomCustomerContactDetails(){
        // Test seeder case where user is a visiting user,
        // and haven't submitted contact info,
        // only 40 user would have contact info, 4 won't
        for($i = 1; $i < 45; $i++) {
            if($i < 40){

                $faker = Factory::create();
                $customerContactDetails = new CustomerContact();
                $customerContactDetails->customer_id = $i;
                $customerContactDetails->email = $faker->email;
                $customerContactDetails->save();

                $faker = Factory::create();
                $customerContactDetails = new CustomerContact();
                $customerContactDetails->customer_id = $i;
                $customerContactDetails->phone = $faker->phoneNumber;
                $customerContactDetails->save();
            }
        }
    }
}

