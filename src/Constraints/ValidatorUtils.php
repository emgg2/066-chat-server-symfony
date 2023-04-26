<?php


namespace App\Constraints;

use Symfony\Component\HttpFoundation\Request;

class ValidatorUtils
{

    public function getParams( Request $req)
    {
        $parameters = [];


        $params = json_decode($req->getContent()) ;

        if( $params) {
            foreach ($params as $param => $value) {
                $parameters[$param] = $value;
            }
        }
        return $parameters;

    }


    public function getErrorResponse ( $errors ): array
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


}