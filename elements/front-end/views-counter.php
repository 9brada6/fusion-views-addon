<script type="text/html" id="tmpl-fusion_views_addon-shortcode">
	<style>{{{ customStyle }}}}</style>
	<div {{{ _.fusionGetAttributes( wrapperAttributes ) }}}>
		<div {{{ _.fusionGetAttributes( separatorAttributes ) }}} >
			<div {{{ _.fusionGetAttributes( contentAttributes ) }}}>
				{{{ FusionPageBuilderApp.renderContent( mainContent, cid, false ) }}}
			</div>
		</div>
	</div>
</script>
