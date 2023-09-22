/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { useEntityProp } from '@wordpress/core-data';
import { CheckboxControl, PanelRow } from '@wordpress/components';

/**
 * Internal dependencies
 */

function SettingsPanelPlugin() {
	const [meta, setMeta] = useEntityProp('postType', 'mailster-form', 'meta');

	const { recaptcha } = meta;

	const title = recaptcha
		? __('Captcha enabled', 'mailster')
		: __('Captcha disabled', 'mailster');

	return (
		<PluginDocumentSettingPanel name="recaptcha" title={title}>
			<PanelRow>
				<CheckboxControl
					label={__('Enable reCaptcha', 'mailster')}
					help={__('Enable reCaptcha for this form.', 'mailster')}
					checked={!!recaptcha}
					onChange={() => setMeta({ recaptcha: !recaptcha })}
				/>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin('mailster-recpatcha-settings-panel', {
	render: SettingsPanelPlugin,
	icon: false,
});
