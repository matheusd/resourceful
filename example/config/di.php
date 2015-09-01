<?php

class WebAppResourceNotFoundException extends Exception { }
class WebAppRouteNotFoundException extends Exception { }

class WebAppDIProvider implements Pimple\ServiceProviderInterface
{
    public function register(Pimple\Container $c)
    {
        include(__DIR__.'/../local/config.php');
        foreach ($env as $envk => $envval) {
            $c["config/$envk"] = $envval;
        }
    
        $c['routes'] = [
            '/' => 'route/index', 
            '/test',
            '/index',
            '/form',
        ];        
        
        $c['entityManager'] = function ($c) {
            $config = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(__DIR__."/orm"), $c['config/devVersion']);
            $conn = $c['config/databases']['default'];
            return Doctrine\ORM\EntityManager::create($conn, $config);            
        };
        
        $c['dispatcher'] = function ($c) {
            $routes = $c['routes'];
            $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($routes) {
                foreach ($routes as $k => $v) {
                    if (is_int($k)) {
                        $k = $v;
                        $v = "route$v";
                    }
                    $r->addRoute('*', $k, $v);
                }                
            });  
            return $dispatcher;
        };
        
        $c['request'] = function ($c) {
            $req = Zend\Diactoros\ServerRequestFactory::fromGlobals(
                $_SERVER,
                $_GET,
                $_POST,
                $_COOKIE,
                $_FILES
            );     
            $c['logger']->notice('Started ' . $req->getMethod() . ' ' . $req->getUri()->getPath());
            return $req;
        };
        
        $c['resource'] = function ($c) {
            $dispatcher = $c['dispatcher'];
            $request = $c['request'];
            $uri = $request->getUri();
            $path = $uri->getPath();            
            if (preg_match("|^(.+)\..+$|", $path, $matches)) {
                //if path ends in .json, .html, etc, ignore it
                $path = $matches[1];
            }            
            $res = $dispatcher->dispatch('*', $path);
            if ($res[0] == FastRoute\Dispatcher::NOT_FOUND) {
                throw new WebAppRouteNotFoundException("Route '$path' not found on routing table"); 
            }            
            
            $reqParameters = $res[2];
            $c['requestParameters'] = $reqParameters;
            
            $entry = $res[1];            
            
            if (!isset($c[$entry])) {
                throw new WebAppResourceNotFoundException("Resource '$entry' not found on DI container");
            }
            
            $res = $c[$entry];
            $c['logger']->notice("Resource Selected ($entry): " . get_class($res));
            return $res;
        };
        
        $c['response'] = function ($c) {            
            try {
                $resource = $c['resource'];
                return $resource->exec();
            } catch (Exception $e) {
                return $c['handleException']($e);
            }
        };
        
        $c['templaterFactory'] = function ($c) {
            $temp = new ExampleApp\templater\SampleTemplaterFactory();
            $temp->globalContext = [
                'url' => $c['config/publicUrl'],
                'assetsUrl' => $c['config/assetsUrl'],
            ];
            return $temp;
        };
        
        $c['responseFactory'] = function ($c) {
            $respFactory = new Resourceful\ResponseFactory();
            $respFactory->templaterFactory = $c['templaterFactory'];
            return $respFactory;
        };
        
        $c['responseEmitter'] = function ($c) {
            return new Zend\Diactoros\Response\SapiEmitter();
        };
        
        $c['session'] = function ($c) {
            $sess = new Resourceful\SessionStorage("ExampleApp");
            $sess->startSession();
            return $sess;
        };
        
        $c['logger'] = function ($c) {            
            $handler = new Monolog\Handler\ErrorLogHandler(Monolog\Handler\ErrorLogHandler::SAPI, Monolog\Logger::NOTICE);
            $formatter = new Monolog\Formatter\LineFormatter();
            $formatter->includeStacktraces(true);
            $handler->setFormatter($formatter);
            $log = new Monolog\Logger('webapp');
            $log->pushHandler($handler);                        
            return $log;
        };
        
        $c['handleException'] = $c->protect(function ($e) use ($c) {
            $html = "<p>Internal Server Error</p>" .
                "<pre>".$e."</pre>";
            $resp = new Zend\Diactoros\Response\HtmlResponse($html, 500);
            $c['logger']->error($e);
            return $resp;
        });        
 

        $mkres = function ($cls) use ($c) {   
            return function ($c) use ($cls) {
                $res = new $cls();
                $res->request = $c['request'];
                $res->parameters = $c['requestParameters'];
                $res->responseFactory = $c['responseFactory'];
                $res->session = $c['session'];
                return $res;
            };
        };
        
        
        $c['route/index'] = $mkres('ExampleApp\Home\Control\IndexResource');
        $c['route/form'] = $mkres('ExampleApp\Home\Control\FormResource');
                
    }
}