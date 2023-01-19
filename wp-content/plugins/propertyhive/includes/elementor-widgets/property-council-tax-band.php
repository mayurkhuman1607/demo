<?php
/**
 * Elementor Property Council Tax Band Widget.
 *
 * @since 1.0.0
 */
class Elementor_Property_Council_Tax_Band_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'property-council-tax-band';
	}

	public function get_title() {
		return __( 'Council Tax Band', 'propertyhive' );
	}

	public function get_icon() {
		return 'fas fa-info';
	}

	public function get_categories() {
		return [ 'property-hive' ];
	}

	public function get_keywords() {
		return [ 'property hive', 'propertyhive', 'property', 'council tax' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Council Tax Band', 'propertyhive' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'propertyhive' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-info',
					'library' => 'solid',
				],
			]
		);

		$this->add_control(
			'before',
			[
				'label' => __( 'Before', 'propertyhive' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'after',
			[
				'label' => __( 'After', 'propertyhive' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Council Tax Band', 'propertyhive' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'label' => __( 'Typography', 'propertyhive' ),
				'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .elementor-widget-council-tax-band',
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Colour', 'propertyhive' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-council-tax-band' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'text_align',
			[
				'label' => __( 'Alignment', 'propertyhive' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'propertyhive' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'propertyhive' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'propertyhive' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-council-tax-band' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		global $property;

		$settings = $this->get_settings_for_display();

		if ( !isset($property->id) ) {
			return;
		}

		if ( $property->council_tax_band != '' )
		{
	        echo '<div class="elementor-widget-council-tax-band">';
	        if ( isset($settings['icon']) && !empty($settings['icon']) )
	        {
	        	\Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
	        	echo ' ';
	        }
	        if ( isset($settings['before']) && !empty($settings['before']) )
	        {
	        	echo $settings['before'] . ' ';
	        }
			echo $property->council_tax_band;
	        if ( isset($settings['after']) && !empty($settings['after']) )
	        {
	        	echo ' ' . $settings['after'];
	        }
	        echo '</div>';
	    }

	}

}