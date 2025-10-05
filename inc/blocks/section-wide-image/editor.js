(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps, LinkControl } = wp.blockEditor;
    const { Button, PanelBody, PanelRow, ToggleControl, SelectControl, RangeControl, TextControl, BaseControl, ColorPicker, Placeholder, Spinner } = wp.components;
    const { Fragment, createElement, useState } = wp.element;
    const { __ } = wp.i18n;

    registerBlockType('julianboelen/section-wide-image', {
        apiVersion: 2,
        title: __('Section Wide Image', 'julianboelen'),
        icon: 'format-image',
        category: 'julianboelen-blocks',
        description: __('Full-width image section with animation support and advanced image controls', 'julianboelen'),
        supports: {
            html: false,
            anchor: true,
            customClassName: true,
            inserter: true,
            multiple: true,
            reusable: true,
            spacing: {
                padding: true,
                margin: true
            },
            align: ['wide', 'full']
        },
        
        attributes: {
            imageUrl: { type: 'string', default: 'https://images.unsplash.com/photo-1575936123452-b67c3203c357?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHwxfHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1920' },
            imageId: { type: 'number', default: 0 },
            imageAlt: { type: 'string', default: 'Collaborative workspace with laptops, tablets, notebooks and coffee on wooden table' },
            imageWidth: { type: 'number', default: 1920 },
            imageHeight: { type: 'number', default: 800 },
            objectFit: { type: 'string', default: 'cover', enum: ['cover', 'contain', 'fill', 'none', 'scale-down'] },
            borderRadius: { type: 'string', default: '2xl', enum: ['none', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', 'full'] },
            enableAnimation: { type: 'boolean', default: true },
            animationType: { type: 'string', default: 'fade-up', enum: ['fade', 'fade-up', 'fade-down', 'fade-left', 'fade-right', 'zoom-in', 'zoom-out', 'slide-up', 'slide-down', 'flip-left', 'flip-right'] },
            animationDuration: { type: 'number', default: 1200 },
            animationDelay: { type: 'number', default: 0 },
            animationEasing: { type: 'string', default: 'ease-in-out', enum: ['linear', 'ease', 'ease-in', 'ease-out', 'ease-in-out', 'ease-in-back', 'ease-out-back', 'ease-in-out-back'] },
            aspectRatio: { type: 'string', default: 'auto', enum: ['auto', '16/9', '4/3', '3/2', '21/9', '1/1'] },
            maxHeight: { type: 'string', default: 'none' },
            overlayEnabled: { type: 'boolean', default: false },
            overlayColor: { type: 'string', default: 'rgba(0, 0, 0, 0.3)' },
            overlayOpacity: { type: 'number', default: 30 },
            linkUrl: { type: 'string', default: '' },
            linkTarget: { type: 'string', default: '' },
            linkRel: { type: 'string', default: '' },
            enableLazyLoad: { type: 'boolean', default: true },
            containerPadding: { type: 'string', default: 'none', enum: ['none', 'sm', 'md', 'lg', 'xl'] }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                imageUrl,
                imageId,
                imageAlt,
                imageWidth,
                imageHeight,
                objectFit,
                borderRadius,
                enableAnimation,
                animationType,
                animationDuration,
                animationDelay,
                animationEasing,
                aspectRatio,
                maxHeight,
                overlayEnabled,
                overlayColor,
                overlayOpacity,
                linkUrl,
                linkTarget,
                linkRel,
                enableLazyLoad,
                containerPadding
            } = attributes;

            const [showLinkControl, setShowLinkControl] = useState(false);
            const [linkValue, setLinkValue] = useState({
                url: linkUrl,
                opensInNewTab: linkTarget === '_blank'
            });

            // SOPHISTICATED helper functions for dynamic styling
            const getBorderRadiusClass = () => {
                const radiusMap = {
                    'none': 'rounded-none',
                    'sm': 'rounded-sm',
                    'md': 'rounded-md',
                    'lg': 'rounded-lg',
                    'xl': 'rounded-xl',
                    '2xl': 'rounded-2xl',
                    '3xl': 'rounded-3xl',
                    'full': 'rounded-full'
                };
                return radiusMap[borderRadius] || 'rounded-2xl';
            };

            const getObjectFitClass = () => {
                const fitMap = {
                    'cover': 'object-cover',
                    'contain': 'object-contain',
                    'fill': 'object-fill',
                    'none': 'object-none',
                    'scale-down': 'object-scale-down'
                };
                return fitMap[objectFit] || 'object-cover';
            };

            const getAspectRatioClass = () => {
                if (aspectRatio === 'auto') return '';
                const ratioMap = {
                    '16/9': 'aspect-video',
                    '4/3': 'aspect-[4/3]',
                    '3/2': 'aspect-[3/2]',
                    '21/9': 'aspect-[21/9]',
                    '1/1': 'aspect-square'
                };
                return ratioMap[aspectRatio] || '';
            };

            const getContainerPaddingClass = () => {
                const paddingMap = {
                    'none': '',
                    'sm': 'px-4',
                    'md': 'px-6 md:px-8',
                    'lg': 'px-8 md:px-12',
                    'xl': 'px-12 md:px-16 lg:px-20'
                };
                return paddingMap[containerPadding] || '';
            };

            const getAnimationAttributes = () => {
                if (!enableAnimation) return {};
                return {
                    'data-aos': animationType,
                    'data-aos-duration': animationDuration,
                    'data-aos-delay': animationDelay,
                    'data-aos-easing': animationEasing
                };
            };

            const parseRgbaColor = (rgba) => {
                const match = rgba.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
                if (match) {
                    return {
                        r: parseInt(match[1]),
                        g: parseInt(match[2]),
                        b: parseInt(match[3]),
                        a: match[4] ? parseFloat(match[4]) : 1
                    };
                }
                return { r: 0, g: 0, b: 0, a: 0.3 };
            };

            const rgbaToHex = (rgba) => {
                const color = parseRgbaColor(rgba);
                const toHex = (n) => {
                    const hex = Math.round(n).toString(16);
                    return hex.length === 1 ? '0' + hex : hex;
                };
                return `#${toHex(color.r)}${toHex(color.g)}${toHex(color.b)}`;
            };

            const hexToRgba = (hex, opacity) => {
                const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                if (result) {
                    const r = parseInt(result[1], 16);
                    const g = parseInt(result[2], 16);
                    const b = parseInt(result[3], 16);
                    return `rgba(${r}, ${g}, ${b}, ${opacity / 100})`;
                }
                return `rgba(0, 0, 0, ${opacity / 100})`;
            };

            const handleLinkChange = (newLink) => {
                setLinkValue(newLink);
                setAttributes({
                    linkUrl: newLink.url,
                    linkTarget: newLink.opensInNewTab ? '_blank' : '',
                    linkRel: newLink.opensInNewTab ? 'noopener noreferrer' : ''
                });
            };

            const onSelectImage = (media) => {
                setAttributes({
                    imageUrl: media.url,
                    imageId: media.id,
                    imageAlt: media.alt || '',
                    imageWidth: media.width || 1920,
                    imageHeight: media.height || 800
                });
            };

            const onRemoveImage = () => {
                setAttributes({
                    imageUrl: '',
                    imageId: 0,
                    imageAlt: ''
                });
            };

            const handleOverlayColorChange = (color) => {
                const newColor = hexToRgba(color.hex, overlayOpacity);
                setAttributes({ overlayColor: newColor });
            };

            const handleOverlayOpacityChange = (value) => {
                const hexColor = rgbaToHex(overlayColor);
                const newColor = hexToRgba(hexColor, value);
                setAttributes({ 
                    overlayOpacity: value,
                    overlayColor: newColor 
                });
            };

            // Build dynamic styles for editor preview
            const imageStyles = {
                maxHeight: maxHeight !== 'none' ? maxHeight : undefined
            };

            const overlayStyles = overlayEnabled ? {
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                bottom: 0,
                backgroundColor: overlayColor,
                pointerEvents: 'none',
                borderRadius: 'inherit'
            } : {};

            // ADVANCED editor preview with realistic styling
            return createElement(Fragment, null,
                // SOPHISTICATED InspectorControls with multiple panels
                createElement(InspectorControls, null,
                    createElement(PanelBody, { 
                        title: __('Image Settings', 'julianboelen'), 
                        initialOpen: true 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(MediaUploadCheck, null,
                                    createElement(MediaUpload, {
                                        onSelect: onSelectImage,
                                        allowedTypes: ['image'],
                                        value: imageId,
                                        render: ({ open }) => createElement('div', null,
                                            createElement(Button, {
                                                onClick: open,
                                                variant: 'secondary',
                                                style: { marginBottom: '10px', width: '100%' }
                                            }, imageId ? __('Replace Image', 'julianboelen') : __('Select Image', 'julianboelen')),
                                            imageId && createElement(Button, {
                                                onClick: onRemoveImage,
                                                variant: 'tertiary',
                                                isDestructive: true,
                                                style: { width: '100%' }
                                            }, __('Remove Image', 'julianboelen'))
                                        )
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(TextControl, {
                                    label: __('Alt Text', 'julianboelen'),
                                    value: imageAlt,
                                    onChange: (value) => setAttributes({ imageAlt: value }),
                                    help: __('Describe the image for accessibility', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Object Fit', 'julianboelen'),
                                    value: objectFit,
                                    options: [
                                        { label: __('Cover', 'julianboelen'), value: 'cover' },
                                        { label: __('Contain', 'julianboelen'), value: 'contain' },
                                        { label: __('Fill', 'julianboelen'), value: 'fill' },
                                        { label: __('None', 'julianboelen'), value: 'none' },
                                        { label: __('Scale Down', 'julianboelen'), value: 'scale-down' }
                                    ],
                                    onChange: (value) => setAttributes({ objectFit: value }),
                                    help: __('How the image should fit within its container', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Aspect Ratio', 'julianboelen'),
                                    value: aspectRatio,
                                    options: [
                                        { label: __('Auto', 'julianboelen'), value: 'auto' },
                                        { label: __('16:9 (Video)', 'julianboelen'), value: '16/9' },
                                        { label: __('4:3', 'julianboelen'), value: '4/3' },
                                        { label: __('3:2', 'julianboelen'), value: '3/2' },
                                        { label: __('21:9 (Ultrawide)', 'julianboelen'), value: '21/9' },
                                        { label: __('1:1 (Square)', 'julianboelen'), value: '1/1' }
                                    ],
                                    onChange: (value) => setAttributes({ aspectRatio: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(TextControl, {
                                    label: __('Max Height', 'julianboelen'),
                                    value: maxHeight,
                                    onChange: (value) => setAttributes({ maxHeight: value }),
                                    help: __('e.g., 800px, 50vh, or "none"', 'julianboelen'),
                                    placeholder: 'none'
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement(ToggleControl, {
                                label: __('Enable Lazy Loading', 'julianboelen'),
                                checked: enableLazyLoad,
                                onChange: (value) => setAttributes({ enableLazyLoad: value }),
                                help: __('Improves page load performance', 'julianboelen')
                            })
                        )
                    ),
                    
                    createElement(PanelBody, { 
                        title: __('Design Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Border Radius', 'julianboelen'),
                                    value: borderRadius,
                                    options: [
                                        { label: __('None', 'julianboelen'), value: 'none' },
                                        { label: __('Small', 'julianboelen'), value: 'sm' },
                                        { label: __('Medium', 'julianboelen'), value: 'md' },
                                        { label: __('Large', 'julianboelen'), value: 'lg' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xl' },
                                        { label: __('2X Large', 'julianboelen'), value: '2xl' },
                                        { label: __('3X Large', 'julianboelen'), value: '3xl' },
                                        { label: __('Full', 'julianboelen'), value: 'full' }
                                    ],
                                    onChange: (value) => setAttributes({ borderRadius: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Container Padding', 'julianboelen'),
                                    value: containerPadding,
                                    options: [
                                        { label: __('None', 'julianboelen'), value: 'none' },
                                        { label: __('Small', 'julianboelen'), value: 'sm' },
                                        { label: __('Medium', 'julianboelen'), value: 'md' },
                                        { label: __('Large', 'julianboelen'), value: 'lg' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xl' }
                                    ],
                                    onChange: (value) => setAttributes({ containerPadding: value }),
                                    help: __('Horizontal padding around the image', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement(ToggleControl, {
                                label: __('Enable Overlay', 'julianboelen'),
                                checked: overlayEnabled,
                                onChange: (value) => setAttributes({ overlayEnabled: value }),
                                help: __('Add a color overlay on top of the image', 'julianboelen')
                            })
                        ),
                        overlayEnabled && createElement(Fragment, null,
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(BaseControl, {
                                        label: __('Overlay Color', 'julianboelen')
                                    },
                                        createElement(ColorPicker, {
                                            color: rgbaToHex(overlayColor),
                                            onChange: handleOverlayColorChange,
                                            disableAlpha: true
                                        })
                                    )
                                )
                            ),
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(RangeControl, {
                                        label: __('Overlay Opacity', 'julianboelen'),
                                        value: overlayOpacity,
                                        onChange: handleOverlayOpacityChange,
                                        min: 0,
                                        max: 100,
                                        step: 5
                                    })
                                )
                            )
                        )
                    ),
                    
                    createElement(PanelBody, { 
                        title: __('Animation Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement(ToggleControl, {
                                label: __('Enable Animation', 'julianboelen'),
                                checked: enableAnimation,
                                onChange: (value) => setAttributes({ enableAnimation: value }),
                                help: __('Animate image on scroll using AOS library', 'julianboelen')
                            })
                        ),
                        enableAnimation && createElement(Fragment, null,
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(SelectControl, {
                                        label: __('Animation Type', 'julianboelen'),
                                        value: animationType,
                                        options: [
                                            { label: __('Fade', 'julianboelen'), value: 'fade' },
                                            { label: __('Fade Up', 'julianboelen'), value: 'fade-up' },
                                            { label: __('Fade Down', 'julianboelen'), value: 'fade-down' },
                                            { label: __('Fade Left', 'julianboelen'), value: 'fade-left' },
                                            { label: __('Fade Right', 'julianboelen'), value: 'fade-right' },
                                            { label: __('Zoom In', 'julianboelen'), value: 'zoom-in' },
                                            { label: __('Zoom Out', 'julianboelen'), value: 'zoom-out' },
                                            { label: __('Slide Up', 'julianboelen'), value: 'slide-up' },
                                            { label: __('Slide Down', 'julianboelen'), value: 'slide-down' },
                                            { label: __('Flip Left', 'julianboelen'), value: 'flip-left' },
                                            { label: __('Flip Right', 'julianboelen'), value: 'flip-right' }
                                        ],
                                        onChange: (value) => setAttributes({ animationType: value })
                                    })
                                )
                            ),
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(RangeControl, {
                                        label: __('Animation Duration (ms)', 'julianboelen'),
                                        value: animationDuration,
                                        onChange: (value) => setAttributes({ animationDuration: value }),
                                        min: 200,
                                        max: 3000,
                                        step: 100
                                    })
                                )
                            ),
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(RangeControl, {
                                        label: __('Animation Delay (ms)', 'julianboelen'),
                                        value: animationDelay,
                                        onChange: (value) => setAttributes({ animationDelay: value }),
                                        min: 0,
                                        max: 2000,
                                        step: 100,
                                        help: __('Delay before animation starts', 'julianboelen')
                                    })
                                )
                            ),
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(SelectControl, {
                                        label: __('Animation Easing', 'julianboelen'),
                                        value: animationEasing,
                                        options: [
                                            { label: __('Linear', 'julianboelen'), value: 'linear' },
                                            { label: __('Ease', 'julianboelen'), value: 'ease' },
                                            { label: __('Ease In', 'julianboelen'), value: 'ease-in' },
                                            { label: __('Ease Out', 'julianboelen'), value: 'ease-out' },
                                            { label: __('Ease In Out', 'julianboelen'), value: 'ease-in-out' },
                                            { label: __('Ease In Back', 'julianboelen'), value: 'ease-in-back' },
                                            { label: __('Ease Out Back', 'julianboelen'), value: 'ease-out-back' },
                                            { label: __('Ease In Out Back', 'julianboelen'), value: 'ease-in-out-back' }
                                        ],
                                        onChange: (value) => setAttributes({ animationEasing: value })
                                    })
                                )
                            )
                        )
                    ),
                    
                    createElement(PanelBody, { 
                        title: __('Link Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(Button, {
                                    variant: showLinkControl ? 'secondary' : 'primary',
                                    onClick: () => setShowLinkControl(!showLinkControl),
                                    style: { width: '100%' }
                                }, showLinkControl ? __('Hide Link Settings', 'julianboelen') : __('Add Link to Image', 'julianboelen')),
                                showLinkControl && createElement('div', { style: { marginTop: '15px' } },
                                    createElement(LinkControl, {
                                        value: linkValue,
                                        onChange: handleLinkChange,
                                        settings: [
                                            {
                                                id: 'opensInNewTab',
                                                title: __('Open in new tab', 'julianboelen')
                                            }
                                        ]
                                    }),
                                    linkUrl && createElement(Button, {
                                        variant: 'tertiary',
                                        isDestructive: true,
                                        onClick: () => {
                                            setAttributes({ linkUrl: '', linkTarget: '', linkRel: '' });
                                            setLinkValue({ url: '', opensInNewTab: false });
                                            setShowLinkControl(false);
                                        },
                                        style: { marginTop: '10px', width: '100%' }
                                    }, __('Remove Link', 'julianboelen'))
                                )
                            )
                        )
                    )
                ),
                
                // PROFESSIONAL editor preview with realistic styling
                createElement('div', { 
                    ...useBlockProps({
                        className: 'section-wide-image-block-editor w-full overflow-hidden ' + getContainerPaddingClass(),
                        style: {
                            border: '2px dashed #ccc',
                            borderRadius: '8px',
                            padding: '20px',
                            backgroundColor: '#f9fafb'
                        }
                    })
                },
                    !imageUrl ? createElement(Placeholder, {
                        icon: 'format-image',
                        label: __('Section Wide Image', 'julianboelen'),
                        instructions: __('Select an image to display in full width', 'julianboelen')
                    },
                        createElement(MediaUploadCheck, null,
                            createElement(MediaUpload, {
                                onSelect: onSelectImage,
                                allowedTypes: ['image'],
                                value: imageId,
                                render: ({ open }) => createElement(Button, {
                                    onClick: open,
                                    variant: 'primary'
                                }, __('Select Image', 'julianboelen'))
                            })
                        )
                    ) : createElement('div', {
                        className: 'w-full relative',
                        ...getAnimationAttributes()
                    },
                        createElement('div', {
                            className: 'relative ' + getAspectRatioClass(),
                            style: { position: 'relative' }
                        },
                            createElement('img', {
                                src: imageUrl,
                                alt: imageAlt || __('Section image', 'julianboelen'),
                                className: 'w-full h-auto ' + getObjectFitClass() + ' ' + getBorderRadiusClass(),
                                style: imageStyles,
                                loading: enableLazyLoad ? 'lazy' : 'eager'
                            }),
                            overlayEnabled && createElement('div', {
                                className: getBorderRadiusClass(),
                                style: overlayStyles
                            }),
                            enableAnimation && createElement('div', {
                                style: {
                                    position: 'absolute',
                                    top: '10px',
                                    right: '10px',
                                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                                    color: '#fff',
                                    padding: '5px 10px',
                                    borderRadius: '4px',
                                    fontSize: '12px',
                                    fontWeight: 'bold'
                                }
                            }, __('Animation: ', 'julianboelen') + animationType)
                        )
                    )
                )
            );
        },

        save: function() {
            return null; // Server-side rendering
        }
    });
})();