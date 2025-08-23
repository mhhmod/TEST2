/**
 * Block Editor Script for GrindCTRL Theme
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Wait for the DOM to be ready
    wp.domReady(function() {
        
        // Unregister default block styles that don't fit the theme
        wp.blocks.unregisterBlockStyle('core/button', 'default');
        wp.blocks.unregisterBlockStyle('core/button', 'outline');
        wp.blocks.unregisterBlockStyle('core/button', 'squared');
        
        // Register custom button styles
        wp.blocks.registerBlockStyle('core/button', {
            name: 'grindctrl-primary',
            label: 'Primary Button',
            isDefault: true
        });
        
        wp.blocks.registerBlockStyle('core/button', {
            name: 'grindctrl-secondary',
            label: 'Secondary Button'
        });
        
        wp.blocks.registerBlockStyle('core/button', {
            name: 'grindctrl-outline',
            label: 'Outline Button'
        });

        // Register custom quote styles
        wp.blocks.registerBlockStyle('core/quote', {
            name: 'grindctrl-testimonial',
            label: 'Testimonial Style'
        });

        // Register custom separator styles
        wp.blocks.registerBlockStyle('core/separator', {
            name: 'grindctrl-dots',
            label: 'Dotted Style'
        });

        wp.blocks.registerBlockStyle('core/separator', {
            name: 'grindctrl-thick',
            label: 'Thick Line'
        });

        // Register custom heading styles
        wp.blocks.registerBlockStyle('core/heading', {
            name: 'grindctrl-underline',
            label: 'With Underline'
        });

        wp.blocks.registerBlockStyle('core/heading', {
            name: 'grindctrl-highlight',
            label: 'Highlighted'
        });

        // Register custom paragraph styles
        wp.blocks.registerBlockStyle('core/paragraph', {
            name: 'grindctrl-large',
            label: 'Large Text'
        });

        wp.blocks.registerBlockStyle('core/paragraph', {
            name: 'grindctrl-small',
            label: 'Small Text'
        });

        // Register custom list styles
        wp.blocks.registerBlockStyle('core/list', {
            name: 'grindctrl-checkmark',
            label: 'Checkmark List'
        });

        wp.blocks.registerBlockStyle('core/list', {
            name: 'grindctrl-arrow',
            label: 'Arrow List'
        });

        // Register custom image styles
        wp.blocks.registerBlockStyle('core/image', {
            name: 'grindctrl-rounded',
            label: 'Rounded Corners'
        });

        wp.blocks.registerBlockStyle('core/image', {
            name: 'grindctrl-shadow',
            label: 'With Shadow'
        });

        // Register custom group styles
        wp.blocks.registerBlockStyle('core/group', {
            name: 'grindctrl-card',
            label: 'Card Style'
        });

        wp.blocks.registerBlockStyle('core/group', {
            name: 'grindctrl-highlight-box',
            label: 'Highlight Box'
        });

        // Register custom column styles
        wp.blocks.registerBlockStyle('core/columns', {
            name: 'grindctrl-equal-height',
            label: 'Equal Height'
        });

        wp.blocks.registerBlockStyle('core/columns', {
            name: 'grindctrl-card-layout',
            label: 'Card Layout'
        });

    });

    // Add custom color palette
    wp.blocks.registerBlockStyle('core/paragraph', {
        name: 'grindctrl-accent-text',
        label: 'Accent Text Color'
    });

    // Custom block variations (if needed)
    wp.blocks.registerBlockVariation('core/group', {
        name: 'grindctrl-hero-section',
        title: 'Hero Section',
        description: 'A hero section with background and centered content',
        category: 'design',
        icon: 'cover-image',
        attributes: {
            className: 'grindctrl-hero-section'
        },
        innerBlocks: [
            ['core/heading', { level: 1, placeholder: 'Hero Title' }],
            ['core/paragraph', { placeholder: 'Hero description...' }],
            ['core/buttons']
        ]
    });

    wp.blocks.registerBlockVariation('core/columns', {
        name: 'grindctrl-feature-grid',
        title: 'Feature Grid',
        description: 'A three-column feature grid',
        category: 'design',
        icon: 'grid-view',
        attributes: {
            className: 'grindctrl-feature-grid'
        },
        innerBlocks: [
            ['core/column', {}, [
                ['core/image'],
                ['core/heading', { level: 3, placeholder: 'Feature Title' }],
                ['core/paragraph', { placeholder: 'Feature description...' }]
            ]],
            ['core/column', {}, [
                ['core/image'],
                ['core/heading', { level: 3, placeholder: 'Feature Title' }],
                ['core/paragraph', { placeholder: 'Feature description...' }]
            ]],
            ['core/column', {}, [
                ['core/image'],
                ['core/heading', { level: 3, placeholder: 'Feature Title' }],
                ['core/paragraph', { placeholder: 'Feature description...' }]
            ]]
        ]
    });

})();