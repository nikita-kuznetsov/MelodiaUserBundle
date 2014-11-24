<?php

namespace Melodia\UserBundle\Tests\Controller\Api;

use Melodia\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// TODO rewrite all tests, decode json to assoc arrays, write function that will compare input and output arrays

class UserControllerTest extends WebTestCase
{
    /**
     * @return int
     */
    public function testGetAll()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        $jsonResponse = json_decode($client->getResponse()->getContent());
        $this->assertTrue($jsonResponse !== null);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        return count($jsonResponse);
    }

    /**
     * @return \stdClass
     */
    public function testPost()
    {
        $client = static::createClient();

        $client->request('POST', '/api/users', array(
            'username'             => 'username',
            'password'             => 'password',
            'fullName'             => 'fullName',
            'isActive'             => true,
        ));

        $jsonResponse = json_decode($client->getResponse()->getContent());
        $this->assertTrue($jsonResponse !== null);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        return $jsonResponse;
    }

    /**
     * @depends testGetAll
     *
     * @param int $count
     * @return int
     */
    public function testCountIncremented($count)
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        $jsonResponse = json_decode($client->getResponse()->getContent());
        $this->assertTrue($jsonResponse !== null);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($count + 1, count($jsonResponse));

        return count($jsonResponse);
    }

    /**
     * @depends testPost
     *
     * @param \stdClass $object
     */
    public function testGetOne($object)
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/' . $object->id);

        $jsonResponse = json_decode($client->getResponse()->getContent());
        $this->assertTrue($jsonResponse !== null);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($object->username, $jsonResponse->username);
        $this->assertEquals($object->fullName, $jsonResponse->fullName);
        $this->assertEquals($object->isActive, $jsonResponse->isActive);
        $this->assertEquals($object->createdAt, $jsonResponse->createdAt);
    }

    /**
     * @depends testPost
     *
     * @param \stdClass $object
     * @return \stdClass
     */
    public function testPut($object)
    {
        $client = static::createClient();

        $client->request('PUT', '/api/users/' . $object->id, array(
            'username'             => 'UPD. username',
            'password'             => 'UPD. password',
            'fullName'             => 'UPD. fullName',
            'isActive'             => false,
        ));

        $jsonResponse = json_decode($client->getResponse()->getContent());
        $this->assertTrue($jsonResponse !== null);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        return $jsonResponse;
    }

    /**
     * @depends testPut
     *
     * @param \stdClass
     */
    public function testChanged($object)
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/' . $object->id);

        $jsonResponse = json_decode($client->getResponse()->getContent());
        $this->assertTrue($jsonResponse !== null);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($object->username, $jsonResponse->username);
        $this->assertEquals($object->fullName, $jsonResponse->fullName);
        $this->assertEquals($object->isActive, $jsonResponse->isActive);
        $this->assertEquals($object->createdAt, $jsonResponse->createdAt);
    }

    /**
     * @depends testPost
     *
     * @param \stdClass $object
     */
    public function testDelete($object)
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/users/' . $object->id);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Check that object has been deleted
        $client->request('GET', '/api/users/' . $object->id);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * @depends testCountIncremented
     *
     * @param int $count The number of objects after adding the new one
     */
    public function testCountDecremented($count)
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        $jsonResponse = json_decode($client->getResponse()->getContent());
        $this->assertTrue($jsonResponse !== null);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($count - 1, count($jsonResponse));
    }
}
