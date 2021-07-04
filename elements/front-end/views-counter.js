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

				var decorationWrapper = $thisElement.find( '.avada-views-addon-decoration' );

				// The empty text nodes are removed between the HTML tags because
				// they add space when displaying inline-block.
				this.removeTextNodes( decorationWrapper );
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

				var decorationWrapper = $thisElement.find( '.avada-views-addon-decoration' );

				// The empty text nodes are removed between the HTML tags because
				// they add space when displaying inline-block.
				this.removeTextNodes( decorationWrapper );
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
				var templateVariables = {};

				// Validate values.
				this.validateValues( atts.values );

				// Unique ID for this particular element instance, can be useful.
				templateVariables.cid = this.model.get( 'cid' );

				// Attributes for our wrapping element.
				templateVariables.wrapperAttributes = this.buildWrapperAtts( atts.values );
				templateVariables.contentAttributes = this.buildContentAttr( atts.values );
				templateVariables.separatorAttributes = this.buildSeparatorAtts( atts.values );
				templateVariables.customStyle = this.buildCustomStyle( atts.values );

				templateVariables.mainContent = this.replaceViews( atts.values.element_content, atts.extras );

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
				var wrapperAttributes         = _.fusionVisibilityAtts( values.hide_on_mobile, {
					id: 'avada-views-addon-wrapper--' + this.cid,
					class: 'avada-views-addon-wrapper',
					style: ''
				} );

				if ( values.class ) {
					wrapperAttributes.class += ' ' + values.class;
				}

				if ( values.background ) {
					wrapperAttributes.style += 'background-color:' + values.background + ';';
				}

				if ( values.color ) {
					wrapperAttributes.style += 'color:' + values.color + ';';
				}

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

				if ( values.margin_top ) {
					wrapperAttributes.style += 'margin-top:' + values.margin_top + ';';
				}

				if ( values.margin_bottom ) {
					wrapperAttributes.style += 'margin-bottom:' + values.margin_bottom + ';';
				}

				if ( values.margin_left ) {
					wrapperAttributes.style += 'margin-left:' + values.margin_left + ';';
				}

				if ( values.margin_right ) {
					wrapperAttributes.style += 'margin-right:' + values.margin_right + ';';
				}

				return wrapperAttributes;
			},

			buildSeparatorAtts: function( values ) {
				var attr = {};

				if ( _.isString( values.style_type ) ) {
					if ( -1 !== values.style_type.indexOf( 'double' ) || -1 !== values.style_type.indexOf( 'single' ) ) {
						style = values.style_type.replace( ' ', '-' );
						attr.class = 'avada-views-addon-decoration avada-views-addon-decoration--' + style;
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
			buildContentAttr: function( values ) {
				var attr = {};

				if ( _.isString( values.style_type ) ) {
					if ( -1 !== values.style_type.indexOf( 'double' ) || -1 !== values.style_type.indexOf( 'single' ) ) {
						attr.style = 'display: inline-flex;flex-wrap: wrap;flex-direction: column;';
					}
				}

				attr.class = 'avada-views-addon-content';

				return attr;
			},

			buildCustomStyle: function( values ) {
				var style = '';

				if ( _.isString( values.style_type ) ) {
					if ( -1 !== values.style_type.indexOf( 'double' ) || -1 !== values.style_type.indexOf( 'single' ) ) {
						if ( values.separator_color ) {
							style = '#avada-views-addon-wrapper--' + this.cid + ' .avada-views-addon-decoration::before,' +
							'#avada-views-addon-wrapper--' + this.cid + ' .avada-views-addon-decoration::after{border-color:' + values.separator_color + ';}';
						}
					} else if ( -1 !== values.style_type.indexOf( 'underline' ) ) {
						borderStyles = values.style_type.split( ' ' );

						borderBottomColor = values.separator_color;
						if ( ! borderBottomColor ) {
							borderBottomColor = '';
						}

						if ( 0 < borderStyles.length && ( borderStyles[1].includes( 'dashed' ) || borderStyles[1].includes( 'dotted' ) || borderStyles[1].includes( 'solid' ) ) ) {
							style = '#avada-views-addon-wrapper--' + this.cid + '.avada-views-addon-wrapper{border-bottom: 1px ' + borderStyles[1] + borderBottomColor + ';}';
						}
					}
				}

				return style;
			},

			removeTextNodes: function( el ) {
				jQuery( el ).contents().filter( function() {
					return ( 3 == this.nodeType );
				} ).remove();
			},

			replaceViews: function( content, extras ) {
				var totalViews = /%total_views%/gi;
				var todayViews = /%today_views%/gi;

				if ( _.isString( content ) ) {
					content = content.replace( totalViews, extras.total_views_addon );
					content = content.replace( todayViews, extras.today_views_addon );
				}
				return content;
			}

		} );
	} );
} ( jQuery ) );
