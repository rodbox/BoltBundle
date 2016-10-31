<?php
namespace RB\BoltBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Finder\Finder;

class BoltService {



    public function __construct($container, $session, $router, $doctrine)
    {
        $this->container = $container;
        $this->router    = $router;
        $this->session   = $session;
        $this->doctrine  = $doctrine;

        $this->em        = $this->doctrine->getManager();
        $this->data      = [
            'list' => [],
            'view' => 'suggest_default'
        ];

        $this->input = [];

        $this->bolt  = $this->session->get('bolt',[]);
        $this->meta  = $this->bolt['meta'];
    }



    // charge l'item
    function load($request)
    {
        $this->input['select']     = $request->query->get("select",[]);
        $this->input['val']        = $request->query->get("val","");
        
        $this->itemId               = $request->query->get("itemId");
        $this->itemRef              = $request->query->get("itemRef");
        $this->road                 = $request->query->get("road");

        return $this->load_item($this->itemId);    
    }



    function load_item($itemId)
    {
        $item = $this->em
            ->getRepository('RBBoltBundle:Item')
            ->find($itemId);

        $this->metaItem     = $item->getMeta();
        $load_func          = 'load_'.$item->getType()->getName();
        $this->data['list'] = $this->$load_func($item);
        
        $item               = $this->container->get('rb.serializer')->normalize($item);
        
        $item['data']       = $this->data['list'];
        $item['item']       = $this->bolt['meta']['items'][$this->itemRef];
        return $item;
    }



    function get_meta_item($id)
    {
        return $this->meta['items'][$id];
    }



    function load_item_to($itemFrom)
    {
         $metaFrom = $this->get_meta_item($itemFrom);
         $refTo    = $metaFrom['to'][$this->road];
         $idTo     = $this->extract_id($refTo);
         $itemTo   = $this->load_item($idTo);

         return $itemTo;
    }



    function extract_id($ref)
    {
        $refExplode = explode('-',substr($ref,1));

        return $refExplode[0];
    }

    public function initRoad()
    {

        if (isset($this->dataInit['item']['tos'][$this->road]))
            $data = array_flip($this->dataInit['item']['tos'][$this->road]);
        else
            $data= [];

        $this->dataInit['data'] = $data;


        $this->session->set('bolt_init', $this->dataInit);
    }

    public function getRoadData($item)
    {
        return $this->bolt['road_data'][$item];
    }

    public function setRoadData($item, $data)
    {
        return $this->bolt['road_data'][$item]=$data;
    }



    /* TYPE LOADER */

    function load_free ($item){
        return [''];
    }



    function load_data ($item){
        return $this->metaItem['data'];
    }



    function load_function ($item){
        //$this->road           = $this->input['select']['roadinit'];
        $this->item           = $this->get_meta_item($this->itemRef);
        
        $this->dataInit = [
            'road'  => $this->road,
            'item'  => $this->item
        ];

        $this->initRoad();

        return $this->load_item_to($this->itemRef);
    }



    function load_entity ($item){
        $list = $this->em
            ->getRepository('RBUserBundle:User')
            ->findAll();

        return $this
            ->container
            ->get('rb.serializer')
            ->normalize($list);
    }



    function load_json ($item){
        if($this->metaItem['dir']!='')
            return json_decode(file_get_contents($this->metaItem['dir']),true);
    }



    function load_route ($item){

    }



    function load_folder ($item){
        $dir_item = $this->container->getParameter($this->metaItem['folder']);
        $type     = $this->metaItem['type'];
        $finder   = new Finder();
        $finder->$type()->in($dir_item);

        $list     = [];

        foreach ($finder as $file)
            $list[]['name'] = $file->getRelativePathname();

        return $list;
    }



    function load_service ($item){
        $serviceAction = $this->get($this->metaItem['service']);
    }



    function load_init($item)
    {

    }



    function load_form($item)
    {
        # code...
    }



    function load_group($item)
    {
        # code...
    }
}

?>