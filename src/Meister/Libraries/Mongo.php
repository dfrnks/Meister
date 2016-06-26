<?php

namespace Meister\Meister\Libraries;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Meister\Meister\Interfaces\DatabaseInterface;
use Pimple\Container;

class Mongo implements DatabaseInterface{

    private $config = [];
    
    private $app;
    
    private $db;

    public function __construct(array $config,Container $app) {
        $this->config = $config;
        $this->app = $app;
        
        $this->db = $this->connect();
    }

    /**
     * @return DocumentManager
     */
    private function connect(){

        $config = new Configuration();
        $config->setProxyDir($this->app['cache']['doctrine'].'/mongodb/proxy');
        $config->setProxyNamespace('Proxies');
        $config->setHydratorDir($this->app['cache']['doctrine'].'/mongodb/hydrators');
        $config->setHydratorNamespace('Hydrators');

        $anno = [];
        foreach($this->config['modules'] as $app) {
            $dir = $this->app['Modules'].$app.'/Document';
            if(file_exists($dir)) {
                $anno[] = $dir;
            }
        }

        $driverImpl = $config->newDefaultAnnotationDriver($anno);
        $config->setMetadataDriverImpl($driverImpl);

        $config->setDefaultDB($this->config['database']['name']);

        $config->setDefaultCommitOptions(array(
            'safe' => true,
            'fsync' => true
        ));

        return DocumentManager::create(new Connection($this->config['database']['host']), $config);
    }
    
    /**
     * @return DocumentManager
     */
    public function doc(){
        return $this->db;
    }

    /**
     * @param $doc
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function insert($doc, $data) {

        foreach($data as $key => $val) {
            $field = "set".ucfirst($key);

            if(method_exists($doc, $field)){
                $doc->$field($val);
            }
        }

        $violations = Validator::validator()->validate($doc);

        if (count($violations) > 0) {
            $err = [];
            foreach ($violations as $error) {
                $err[$error->getPropertyPath()]= $error->getMessage();
            }

            throw new \Exception(json_encode($err));
        }

        $this->db->persist($doc);

        $this->db->flush();

        return $doc;
    }

    /**
     * @param $doc
     * @param $data
     * @return mixed
     */
    public function update($doc,$data) {
        foreach($data as $key => $val) {
            $field = "set".ucfirst($key);

//            if(method_exists($document, $field)){
//                $document->$field($val);
//            }

            $doc->$field($val);
        }

        $this->db->persist($doc);

        $this->db->flush();

        return $doc;
    }

}