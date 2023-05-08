<?php

namespace App\Traits;

use Doctrine\DBAL\Exception;

trait JWTTrait
{

    /**
     * getMessageErrorResponse is in ValidatorTrait
     */
    /**
     * @param string $token
     * @return bool
     */
    protected function checkJWT( string $token, $jwtProvider ): bool
    {
        try {
            $jwt = $jwtProvider->load($token);
        } catch (Exception $e) {
            $this->errorMessage = $this->getMessageErrorResponse($e->getMessage());
            return false;

        }

        if($jwt->isInvalid()){
            $this->errorMessage = $this->getMessageErrorResponse('Token Invalid');
            return false;

        }

        if($jwt->isExpired()){
            $this->errorMessage = $this->getMessageErrorResponse('Token has expired ');
            return  false;
        }


        return true;

    }

}