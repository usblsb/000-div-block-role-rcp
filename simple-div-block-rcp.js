(function(blocks, element, blockEditor, components, data) {
	var el = element.createElement;
	var InnerBlocks = blockEditor.InnerBlocks;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;

	blocks.registerBlockType('jlmr/container-div', {
		title: 'Contenedor Div Role y Capabilities',
		icon: 'screenoptions',
		category: 'layout',
		attributes: {
			role: {
				type: 'string',
				default: '',
			},
			permission: {
				type: 'string',
				default: '',
			},
		},
		edit: function(props) {
			function updateRole(value) {
				props.setAttributes({ role: value });
			}

			function updatePermission(value) {
				props.setAttributes({ permission: value });
			}

			return el(
				'div',
				{ className: 'jlmr-container-div-block' },
				el(
					blockEditor.InspectorControls,
					{},
					el(
						PanelBody,
						{ title: 'Ajustes Div Role y Capabilities' },
						el(TextControl, {
							value: props.attributes.role,
							onChange: updateRole,
							label: 'Role del Usuario',
						}),
						el(TextControl, {
							value: props.attributes.permission,
							onChange: updatePermission,
							label: 'Role Capabilities',
						})
					)
				),
				el(InnerBlocks)
			);
		},
		save: function(props) {
			return el('div', { className: 'jlmr-container-div-block' }, el(InnerBlocks.Content));
		},
	});
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.data);
