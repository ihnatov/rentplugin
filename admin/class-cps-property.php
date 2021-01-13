<?php

class cps_property {

    public $post_id;
    public $post_name;
    public $post_type;

    public $operation = array();
    public $price_rent;
    public $price_buy;
    public $property_type;
    public $property_meta;
    public $property_taxonomy;


    function __construct( $post ) {
        if (is_object($post)) {

            $this->post_id   = $post->post_ID;
            $this->post_type = $post->post_type;
            $this->post_name = $post->name;

        } elseif (is_numeric($post)) {

        }
    }
}
