<?php

namespace Klevu\Categorynavigation\Model\Api\Request;

class Get extends \Klevu\Search\Model\Api\Request
{

    public function __toString()
    {
        $string = parent::__toString();

        $parameters = $this->getData();
        if (count($parameters) > 0) {
            array_walk($parameters, function (&$value, $key) {
                $value = sprintf("%s: %s", $key, $value);
            });
        }
	
        return sprintf("%s\nGET parameters:\n%s\n", $string, implode("\n", $parameters));
    }

    /**
     * Add GET parameters to the request, force GET method.
     *
     * @return \Zend\Http\Client
     */
    protected function build()
    {
        $client = parent::build();
 
        $client
            ->setMethod(\Zend\Http\Request::METHOD_GET)
            ->setParameterGet($this->getData());

        return $client;
    }
}
