<?php


namespace App\Traits;

use Symfony\Component\HttpFoundation\Request;

trait ValidatorTrait
{

    /**
     * @param Request $req
     * @return array
     */
    protected function getParams( Request $req): array
    {
        $parameters = [];
        $params = json_decode($req->getContent()) ;

        if( $params ) {
            foreach ($params as $param => $value) {
                $parameters[$param] = $value;
            }
        }
        return $parameters;

    }

    /**
     * @param $message
     * @return array
     */
    protected function getMessageResponse ( $message ): array
    {
        return  [
            "ok"  => false,
            "msg" => $message
        ];
    }

    /**
     * @param $errors
     * @return array
     */
    protected function getErrorResponse ( $errors ): array
    {
        $response = [
            "ok"  => false,
            "msg" => []
        ];


        foreach ( $errors as $message)
        {
            $response['msg'][] = [
                'field' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage()
            ];

        }

        return $response;
    }

    /**
     * @param $errors
     * @return array
     */
    protected function getMessageErrorResponse ( $message ): array
    {
        return [
            "ok"  => false,
            "msg" => $message
        ];

    }

    /**
     * @param array $data
     * @return array
     */
    protected function getOKResponse ( array $data ): array
    {
        $response =  [
            "ok" => true
        ];

        foreach ($data as $namefield => $value)
        {
            $response [$namefield] = $value;
        }

        return $response;

    }

}