<?php

namespace tests\Api\Contact;

use AbuseIO\Models\Contact;
use AbuseIO\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use tests\TestCase;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    const URL = '/api/d41d8cd98f00b204e8000998ecf8427e/v1/contacts';

    public function testValidationErrors()
    {
        $response = $this->call([]);

        $this->assertContains(
            'The name field is required.',
            $response->getContent()
        );
    }

    public function testSuccesfullCreate()
    {
        $contact = factory(Contact::class)->make()->toArray();

        $response = $this->call($contact);

        $this->assertTrue(
            $response->isSuccessful()
        );

        $obj = $response->getContent();

        unset($contact['account_id']);

        foreach ($contact as $value) {
            if ($value && $value != 1) {
                $value = str_replace('/', '\/', $value);
                $this->assertContains(
                    $value,
                    $obj
                );
            }
        }
    }

    public function call($parameters)
    {
        $user = User::find(1);
        $this->actingAs($user);

        $server = $this->transformHeadersToServerVars(['Accept' => 'application/json']);

        return parent::call('POST', self::URL, $parameters, [], [], $server);
    }
}