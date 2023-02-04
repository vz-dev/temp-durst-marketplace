<?php
/**
 * Durst - project - SchemaValidatorTrait.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.04.18
 * Time: 12:08
 */

namespace Pyz\Yves\AppRestApi\Validator;

use JsonSchema\Validator;

trait SchemaValidatorTrait
{
    /**
     * @var bool
     */
    protected $isValid;

    /**
     * @var \stdClass
     */
    protected $errors;

    /**
     * @param \stdClass|null $json
     * @param string $schemaPath
     */
    protected function validate(\stdClass $json = null, string $schemaPath) : void
    {
        $validator = new Validator();

        if($json === null){
            $this->errors = new \stdClass();
            $this->errors->errors = [];
            $errorObject = new \stdClass();
            $errorObject->property = 'json';
            $errorObject->message = 'The request content is no valid JSON (missing a comma maybe ;)';

            $this->errors->errors[] = $errorObject;
            $this->isValid = false;

            return;
        }

        $validator->validate($json, (object) ['$ref' => $schemaPath]);

        $this->isValid = $validator->isValid();

        $this->errors = new \stdClass();
        $this->errors->errors = [];
        foreach($validator->getErrors() as $error){
            $errorObject = new \stdClass();
            $errorObject->property = $error['property'];
            $errorObject->message = $error['message'];

            $this->errors->errors[] = $errorObject;
        }
    }

    /**
     * @return \stdClass
     */
    protected function createStdClass() : \stdClass
    {
        return new \stdClass();
    }
}