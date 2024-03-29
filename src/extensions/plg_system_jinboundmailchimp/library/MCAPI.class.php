<?php

use Joomla\Registry\Registry;

/**
 * @package             jInbound
 * @subpackage          plg_system_jinboundmailchimp
 **********************************************
 * jInbound
 * Copyright (c) 2013 Anything-Digital.com
 * Copyright (c) 2018 Open Source Training, LLC
 **********************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.n *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 */
class MCAPI
{
    protected $apiVersion = '3.0';

    /**
     * @var string
     */
    protected $apiHost = 'api.mailchimp.com';

    /**
     * @var int
     */
    protected $timeout = 300;

    /**
     * @var int
     */
    protected $chunkSize = 8192;

    /**
     * @var string
     */
    protected $apikey = null;

    /**
     * @var JHttp
     */
    protected $http = null;

    /**
     * @param string $apikey Your MailChimp apikey
     *
     * @return void
     */
    public function __construct($apikey)
    {
        if (strpos($apikey, '-') !== false) {
            $parts = explode('-', $apikey);
            if ($dataCenter = array_pop($parts)) {
                $this->apikey   = $apikey;
                $this->hostName = $dataCenter . '.api.mailchimp.com';
                $this->http     = new JHttp();
            }
        }
    }

    /**
     * @param string $email
     *
     * @return string
     */
    protected function getSubscriberHash($email)
    {
        return md5(strtolower($email));
    }

    /**
     * Connect to the server and call the requested methods, parsing the result
     * You should never have to call this function manually
     *
     * @param string $task
     * @param array  $params
     * @param string $method
     *
     * @return mixed
     * @throws Exception
     */
    protected function callServer($endpoint, array $params = array(), $method = 'get')
    {
        try {
            if (!$this->apikey) {
                throw new Exception('No API key');
            }

            $url     = sprintf('https://%s/%s/%s', $this->hostName, $this->apiVersion, $endpoint);
            $headers = array(
                'Authorization' => 'Basic ' . base64_encode('jInbound.MCAPI:' . $this->apikey)
            );

            $method = strtolower($method);
            switch ($method) {
                case 'get':
                    $query    = $params ? '?' . http_build_query($params) : '';
                    $response = $this->http->get($url . $query, $headers, $this->timeout);
                    break;

                case 'post':
                case 'patch':
                case 'put':
                    $response = $this->http->$method($url, json_encode($params), $headers, $this->timeout);
                    break;

                case 'delete':
                    $response = $this->http->delete($url, $headers, $this->timeout);
                    break;

                default:
                    if (!method_exists($this->http, $method)) {
                        throw new Exception('Invalid method - ' . $method);
                    } else {
                        throw new Exception('Unimplemented method - ' . $method);
                    }
                    break;
            }

            if ($response->code < 300) {
                return json_decode($response->body);

            } elseif ($response->code < 400) {
                $error = new Exception('Mailchimp tried to redirect', $response->code);

            } else {
                $message = json_decode($response->body);
                $error   = new Exception(
                    sprintf(
                        '%s<br/>Mailchimp Responded with (%s) - %s',
                        $endpoint,
                        $message->status,
                        $message->detail
                    ),
                    $message->status
                );
            }

        } catch (Exception $error) {
        } catch (Throwable $throwable) {
            $error = new Exception($throwable->getMessage(), $throwable->getCode());
        }

        if (empty($error) || !$error instanceof Exception) {
            $error = new Exception('Unknown error trying to connect to Mailchimp', 500);
        }

        throw $error;
    }

    /**
     * @return object[]
     * @throws Exception
     */
    public function getLists()
    {
        $response = $this->callServer('lists');

        $lists = array();
        if (!empty($response->lists)) {
            foreach ($response->lists as $list) {
                $lists[$list->id] = $list;
            }
        }

        return $lists;
    }

    /**
     * @param string $listId
     *
     * @return object[]
     * @throws Exception
     */
    public function getCategories($listId)
    {
        $response = $this->callServer("lists/{$listId}/interest-categories");

        $categories = array();
        if (!empty($response->categories)) {
            foreach ($response->categories as $category) {
                $categories[$category->id] = $category;
            }
        }

        return $categories;
    }

    /**
     * @param string $listId
     * @param string $categoryId
     *
     * @return array
     * @throws Exception
     */
    public function getGroups($listId, $categoryId)
    {
        $groups   = array();
        $response = $this->callServer("lists/{$listId}/interest-categories/{$categoryId}/interests");

        if (!empty($response->interests)) {
            foreach ($response->interests as $group) {
                $groups[$group->id] = $group;
            }
        }

        return $groups;
    }

    /**
     * @param string $listId
     *
     * @return array
     * @throws Exception
     */
    public function getFields($listId)
    {
        // A bit of a punt here. But surely no one has created as many as 50 merge fields?!
        $response = $this->callServer("lists/{$listId}/merge-fields", array('count' => 50));

        return empty($response->merge_fields) ? array() : $response->merge_fields;
    }

    /**
     * @param string $emailAddress
     *
     * @return object[]
     * @throws Exception
     */
    public function getMemberships($emailAddress)
    {
        $lists = array();

        $query    = array(
            'query' => $emailAddress
        );
        $response = $this->callServer('search-members', $query);
        if (!empty($response->exact_matches->members)) {
            foreach ($response->exact_matches->members as $member) {
                $lists[$member->list_id] = $member;
            }
        }

        return $lists;
    }

    /**
     * @param string $email
     * @param string $listId
     * @param bool   $delete
     *
     * @return void
     * @throws Exception
     */
    public function unsubscribe($email, $listId, $delete = false)
    {
        $key = $this->getSubscriberHash($email);
        $url = "lists/{$listId}/members/{$key}";

        if ($delete) {
            $this->callServer($url, array(), 'delete');

        } else {
            $params = array(
                'status' => 'unsubscribed'
            );
            $this->callServer($url, $params, 'patch');
        }
    }

    /**
     * @param string $email
     * @param string $listId
     * @param array  $params
     *
     * @return void
     * @throws Exception
     */
    public function subscribe($email, $listId, array $params = array())
    {
        $params = array_merge(
            array(
                'email_address' => $email,
                'status_if_new' => 'subscribed'
            ),
            $params
        );

        $key = $this->getSubscriberHash($email);
        $url = "lists/{$listId}/members/{$key}";

        $this->callServer($url, $params, 'put');
    }

    /**
     * @param string $email
     * @param string $listId
     * @param array  $params
     *
     * @return void
     * @throws Exception
     */
    public function update($email, $listId, array $params)
    {
        $key = $this->getSubscriberHash($email);
        $url = "lists/{$listId}/members/{$key}";

        $params = array_intersect_key(
            $params,
            array_flip(
                array(
                    'email_address',
                    'email_type',
                    'status',
                    'merge_fields',
                    'interests',
                    'language',
                    'vip',
                    'location',
                    'ip_signup',
                    'timestamp_signup',
                    'ip_opt',
                    'timestamp_opt'
                )
            )
        );

        $this->callServer($url, $params, 'patch');
    }
}
