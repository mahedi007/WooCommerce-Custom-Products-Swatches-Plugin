<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class CPD_Render {

	/**
	 * Attribute name which displays on list
	 * @var $list_attribute
	 */
	protected $list_attribute;

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'after_setup_theme', array( $this, 'init' ) );

		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'render_html' ), 10, 2 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'render_on_list' ) );
	}

	public function init() {
		$this->list_attribute = apply_filters( 'cpd_list_attribute', 'pa_color' );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'cpd_style', CPD_URL . '/assets/css/style.css', array(), CPD_VERSION );
		wp_enqueue_script( 'cpd_script', CPD_URL . '/assets/js/script.js', array( 'jquery' ), CPD_VERSION );
	}

	public function render_html( $html, $args ) {
		$attribute = cpd_get_tax_attribute( $args['attribute'] );

		if ( empty( $attribute ) ) {
			return $html;
		}

		$options   = $args['options'];
		$product   = $args['product'];
		$attribute_name = $args['attribute'];

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute_name ) ) {
			$attributes = $product->get_variation_attributes();
			$options = $attributes[$attribute_name];
		}

		$swv_html = array();

		if ( ! empty( $options ) && $product && taxonomy_exists( $attribute_name ) ) {
			$terms = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) );

			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $options ) ) {
					$swv_html[] = $this->render_button( $term, $attribute_name );
				}
			}
		}

		if ( ! empty( $swv_html ) ) {
			$html = '<div class="swv-btn-group">' . implode('', $swv_html) . '</div>' . $html;
		}

		return '<div class="swv-wrapper">' . $html . '</div>';
	}

	public function render_on_list() {
		/**
		 * @var $product WC_Product
		 */
		global $product;

		if ( ! $product->get_id() || ! $product->is_type( 'variable' ) ) {
			return;
		}

		$variations = $product->get_available_variations();

		$variation_terms = array();

		foreach ( $variations as $key => $variation ) {
			$attr_key = 'attribute_' . $this->list_attribute;

			if ( ! isset( $variation['attributes'][ $attr_key ] )) {
				continue;
			}

			$variation_term_key = $variation['attributes'][$attr_key];

			if ( ! empty( $variation['image']['src'] ) ) {
				$variation_terms[ $variation_term_key ] = array(
					'type'         => '',
					'variation_id' => $variation['variation_id'],
					'src'          => $variation['image']['src'],
					'srcset'       => $variation['image']['srcset'],
					'sizes'        => $variation['image']['sizes'],
					'is_in_stock'  => $variation['is_in_stock'],
				);

				$term = get_term_by( 'slug', $variation_term_key, $this->list_attribute );
				$term_type = get_term_meta( $term->term_id, 'swv_type', true );

				$variation_terms[ $variation_term_key ]['term_id'] = $term->term_id;
				$variation_terms[ $variation_term_key ]['name'] = $term->name;

				if ( in_array( $term_type, array( 'color', 'image', 'label' ) ) ) {
					$variation_terms[ $variation_term_key ]['type'] = $term_type;
					$variation_terms[ $variation_term_key ]['color'] = get_term_meta( $term->id, 'swv_color', true );
				}
			}
		}

		if ( empty( $variation_terms ) ) {
			return;
		}

		$html = array();

		foreach ( $variation_terms as $variation_term ) {
			$btn_attrs = array();

			$btn_attrs[] = 'data-src="' . esc_attr( $variation_term['src'] ) . '"';
			$btn_attrs[] = 'data-srcset="' . esc_attr( $variation_term['srcset'] ) . '"';
			$btn_attrs[] = 'data-sizes="' . esc_attr( $variation_term['sizes'] ) . '"';

			$btn_attrs = implode(' ', $btn_attrs);

			switch ( $variation_term['type'] ) {
				case 'color':
					$color = get_term_meta( $variation_term['term_id'], 'cpd_color', true );

					$html[] = sprintf('<span class="swv-list-btn swv-list-btn-color" %s style="background-color: %s"></span>', $btn_attrs, esc_attr( $color ));
					break;
				case 'label':
					$html[] = sprintf('<span class="swv-list-btn swv-list-btn-label" %s>%s</span>', $btn_attrs, $variation_term['name']);
					break;
			}
		}

		if ( ! empty( $html ) ) {
			echo '';
		}
	}

	public function render_button( $term, $attribute_name ) {
		$result = '';
	
		$enable_cpd_feature = get_option('enable_cpd_feature_option'); // Get the checkbox value
	
		if ($enable_cpd_feature === 'yes') {
			$type = get_term_meta( $term->term_id, 'swv_type', true );
	
			switch ( $type ) {
				case 'color':
					$color = get_term_meta( $term->term_id, 'swv_color', true );
					$result = sprintf( '<span class="swv-button swv-button-color" data-attr="%s" data-val="%s" style="background-color: %s"></span>', esc_attr( $attribute_name ), esc_attr( $term->slug ), esc_attr( $color ) );
					break;
				case 'label':
					$result = sprintf( '<span class="swv-button swv-button-label" data-attr="%s" data-val="%s">%s</span>', esc_attr( $attribute_name ), esc_attr( $term->slug ), esc_html( $term->name ) );
					break;
			}
		} else {
			
			$result = sprintf( '', esc_attr( $attribute_name ), esc_attr( $term->slug ), esc_html( $term->name ) );
		}
	
		return $result;
	}
	
}

return new CPD_Render();
