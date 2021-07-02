/* global fusionAllElements */
var FusionPageBuilder = FusionPageBuilder || {};

( function() {

	jQuery( document ).ready( function() {

		FusionPageBuilder.fusion_views_addon = FusionPageBuilder.ElementView.extend( {

			/**
			 * Runs after element is rendered on load.
			 *
			 * @since 2.0
			 * @returns {void}
			 */
			onRender: function() {
				var $thisElement = jQuery( '#fb-preview' )[0].contentWindow.jQuery( this.$el );

				// Try console logging the element, you can do custom init here for example.
				// console.log( $thisElement );
			},

			/**
			 * Runs before element is removed.
			 *
			 * @since 2.0
			 * @returns {void}
			 */
			beforeRemove: function() {
			},

			/**
			 * Runs after view DOM is patched.
			 *
			 * @since 2.0
			 * @returns {void}
			 */
			beforePatch: function() {
			},

			/**
			 * Runs after view DOM is patched, eg after option change.
			 *
			 * @since 2.0
			 * @returns {void}
			 */
			afterPatch: function() {
				var $thisElement = jQuery( '#fb-preview' )[0].contentWindow.jQuery( this.$el );

				// Try console logging the element, you can do custom init here for example.
				// console.log( $thisElement );
			},

			/**
			 * Modify template attributes.
			 *
			 * @since 2.0
			 * @param {Object} atts - The attributes.
			 * @returns {Object}
			 */
			filterTemplateAtts: function( atts ) {

				// Variables we will pass to the template.
				var templateVariables = {}

				// Validate values.
				this.validateValues( atts.values );

				// Unique ID for this particular element instance, can be useful.
				templateVariables.cid = this.model.get( 'cid' );

				// Attributes for our wrapping element.
				templateVariables.wrapperAttributes = this.buildWrapperAtts( atts.values );
				templateVariables.contentAttributes = this.buildContentAttr( atts.values );
				templateVariables.separatorAttributes = this.buildSeparatorAtts( atts.values );
				templateVariables.customStyle = this.buildCustomStyle( atts.values );

				templateVariables.mainContent       = atts.values.element_content;

				return templateVariables;
			},

			/**
			 * Modify the values, making sure they have correct units etc.
			 *
			 * @since 2.0
			 * @param {Object} values - The values.
			 * @returns {void}
			 */
			validateValues: function( values ) {
				// Note, atts.values is the combination of the defaults and the params.
			},

			/**
			 * Builds attributes.
			 *
			 * @since 2.0
			 * @param {Object} values - The values.
			 * @returns {Object}
			 */
			buildWrapperAtts: function( values ) {
				wrapperAttributes = {
					id: 'avada-views-addon-wrapper--' + this.cid,
					class: 'avada-views-addon-wrapper',
					style: 'color: ' + values.color + '; background-color:' + values.background + ';',
				};

				if ( values.content_align && 'auto' !== values.content_align ) {
					wrapperAttributes.style += 'text-align:' + values.content_align + ';';
				}

				if ( values.font_size && 'auto' !== values.font_size ) {
					wrapperAttributes.style += 'font-size:' + values.font_size + ';';
				}

				if ( values.padding_top ) {
					wrapperAttributes.style += 'padding-top:' + values.padding_top + ';';
				}

				if ( values.padding_bottom ) {
					wrapperAttributes.style += 'padding-bottom:' + values.padding_bottom + ';';
				}

				if ( values.padding_left ) {
					wrapperAttributes.style += 'padding-left:' + values.padding_left + ';';
				}

				if ( values.padding_right ) {
					wrapperAttributes.style += 'padding-right:' + values.padding_right + ';';
				}

				return wrapperAttributes;
			},

			buildSeparatorAtts: function( values ) {
				var attr = {};

				if (typeof values.style_type === 'string' || values.style_type instanceof String) {
					if ( values.style_type.indexOf('double') !== -1 || values.style_type.indexOf('single') !== -1 ) {
						style = values.style_type.replace(" ", "-");
						attr['class'] = 'avada-views-addon-decoration avada-views-addon-decoration--' + style;
					}
				}

				return attr;
			},

			/**
			 * Builds attributes.
			 *
			 * @param {Object} values - The values.
			 * @returns {Object}
			 */
			buildContentAttr: function (values) {
				var attr = {};

				if (typeof values.style_type === 'string' || values.style_type instanceof String) {
					if ( values.style_type.indexOf('double') !== -1 || values.style_type.indexOf('single') !== -1 ) {
						attr['style'] = 'display: inline-flex;flex-wrap: wrap;flex-direction: column;';
					}
				}

				attr['class'] = 'avada-views-addon-content';

				return attr;
			},

			buildCustomStyle: function (values) {
				var style = '';

				if (typeof values.style_type === 'string' || values.style_type instanceof String) {
					if ( values.style_type.indexOf('double') !== -1 || values.style_type.indexOf('single') !== -1 ) {
						if ( values.separator_color ) {
							style = '#avada-views-addon-wrapper--' + this.cid + ' .avada-views-addon-decoration::before,' +
							'#avada-views-addon-wrapper--' + this.cid + ' .avada-views-addon-decoration::after{border-color:' + values.separator_color + ';}';
						}
					} else if ( values.style_type.indexOf('underline') !== -1 ) {
						border_styles = values.style_type.split(' ');

						border_bottom_color = values.separator_color;
						if( ! border_bottom_color ) {
							border_bottom_color = '';
						}

						if ( border_styles.length > 0 && ( border_styles[1].includes( 'dashed' ) || border_styles[1].includes( 'dotted' ) || border_styles[1].includes( 'solid' ) ) ) {
							style = '#avada-views-addon-wrapper--' + this.cid + '.avada-views-addon-wrapper{border-bottom: 1px ' + border_styles[1] + border_bottom_color + ';}';
						}
					}
				}

				return style;
			}

		} );
	} );
} ( jQuery ) );
