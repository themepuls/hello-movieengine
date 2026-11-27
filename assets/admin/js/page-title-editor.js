( function ( wp ) {
	if ( ! wp || ! wp.plugins || ! wp.editPost || ! wp.components || ! wp.blockEditor ) {
		return;
	}

	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var SelectControl = wp.components.SelectControl;
	var TextareaControl = wp.components.TextareaControl;
	var Button = wp.components.Button;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
	var createElement = wp.element.createElement;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var __ = wp.i18n.__;

	var META_KEY = '_hello_movieengine_page_title_mode';
	var META_DESC_KEY = '_hello_movieengine_page_title_description';
	var META_BG_KEY = '_hello_movieengine_page_title_bg_id';

	function PageTitlePanel() {
		var postType = useSelect( function ( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );

		if ( 'page' !== postType ) {
			return null;
		}

		var meta = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		}, [] );

		var mode = meta[ META_KEY ];
		mode = mode === 'show' || mode === 'hide' ? mode : '';

		var customDesc = meta[ META_DESC_KEY ];
		customDesc = typeof customDesc === 'string' ? customDesc : '';

		var bgId = parseInt( meta[ META_BG_KEY ], 10 ) || 0;

		var bgPreviewUrl = useSelect(
			function ( select ) {
				if ( ! bgId ) {
					return '';
				}
				var media = select( 'core' ).getEntityRecord( 'postType', 'attachment', bgId );
				if ( ! media || ! media.source_url ) {
					return '';
				}
				if ( media.media_details && media.media_details.sizes && media.media_details.sizes.medium ) {
					return media.media_details.sizes.medium.source_url;
				}
				return media.source_url;
			},
			[ bgId ]
		);

		var editPost = useDispatch( 'core/editor' ).editPost;

		function patchMeta( key, value ) {
			var nextMeta = Object.assign( {}, meta );
			nextMeta[ key ] = value;
			editPost( { meta: nextMeta } );
		}

		function onModeChange( next ) {
			patchMeta( META_KEY, next || '' );
		}

		function onDescChange( next ) {
			patchMeta( META_DESC_KEY, next || '' );
		}

		function onSelectImage( media ) {
			if ( ! media || ! media.id ) {
				return;
			}
			patchMeta( META_BG_KEY, media.id );
		}

		function onRemoveImage() {
			patchMeta( META_BG_KEY, 0 );
		}

		var imageControls = createElement(
			'div',
			{ className: 'hello-movieengine-page-title-panel__bg' },
			bgPreviewUrl
				? createElement( 'img', {
						src: bgPreviewUrl,
						alt: '',
						style: { display: 'block', maxWidth: '100%', height: 'auto', marginBottom: '8px', borderRadius: '4px' },
				  } )
				: null,
			createElement(
				MediaUploadCheck,
				null,
				createElement( MediaUpload, {
					onSelect: onSelectImage,
					allowedTypes: [ 'image' ],
					value: bgId,
					render: function ( obj ) {
						return createElement(
							Button,
							{
								variant: 'secondary',
								onClick: obj.open,
							},
							bgId ? __( 'Change banner image', 'hello-movieengine' ) : __( 'Select banner image', 'hello-movieengine' )
						);
					},
				} )
			),
			bgId
				? createElement(
						Button,
						{
							variant: 'link',
							isDestructive: true,
							onClick: onRemoveImage,
							style: { marginTop: '4px' },
						},
						__( 'Remove banner image', 'hello-movieengine' )
				  )
				: null
		);

		return createElement(
			PluginDocumentSettingPanel,
			{
				name: 'hello-movieengine-page-title',
				title: __( 'Page title banner', 'hello-movieengine' ),
				className: 'hello-movieengine-page-title-panel',
			},
			createElement( SelectControl, {
				label: __( 'Theme page title section', 'hello-movieengine' ),
				help: __(
					'Override Customizer visibility for this page. Left / center / right alignment comes from Appearance → Customize → Page Title.',
					'hello-movieengine'
				),
				value: mode,
				options: [
					{ label: __( 'Default (Customizer)', 'hello-movieengine' ), value: '' },
					{ label: __( 'Show page title banner', 'hello-movieengine' ), value: 'show' },
					{ label: __( 'Hide page title banner', 'hello-movieengine' ), value: 'hide' },
				],
				onChange: onModeChange,
			} ),
			createElement( TextareaControl, {
				label: __( 'Banner description', 'hello-movieengine' ),
				help: __(
					'Optional subtitle under the title. If empty, the first paragraph in the page content is used. Leave both empty to hide the line.',
					'hello-movieengine'
				),
				value: customDesc,
				onChange: onDescChange,
				rows: 3,
			} ),
			createElement(
				'p',
				{ className: 'components-base-control__label' },
				__( 'Banner background image', 'hello-movieengine' )
			),
			createElement(
				'p',
				{ className: 'components-base-control__help' },
				__(
					'Optional. Overrides the Customizer default. If empty, featured image or the Customizer fallback is used.',
					'hello-movieengine'
				)
			),
			imageControls
		);
	}

	registerPlugin( 'hello-movieengine-page-title', {
		render: PageTitlePanel,
		icon: null,
	} );
} )( window.wp );