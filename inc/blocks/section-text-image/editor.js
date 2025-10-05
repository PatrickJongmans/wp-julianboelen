(function() {
    const { registerBlockType } = wp.blocks;
    const { RichText, InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } = wp.blockEditor;
    const { Button, PanelBody, PanelRow, ToggleControl, SelectControl, ColorPicker, TextControl, BaseControl, RangeControl } = wp.components;
    const { Fragment, createElement, useState } = wp.element;
    const { __ } = wp.i18n;

    registerBlockType('julianboelen/section-text-image', {
        apiVersion: 2,
        title: __('Section Text Image', 'julianboelen'),
        icon: 'align-pull-left',
        category: 'julianboelen-blocks',
        description: __('A sophisticated two-column section with text content and image, featuring responsive design and advanced customization options', 'julianboelen'),
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
            typography: {
                fontSize: true,
                lineHeight: true
            }
        },
        
        attributes: {
            smallHeading: { type: 'string', default: 'Dit is Starapple:' },
            mainHeading: { type: 'string', default: 'De specialist achter de match' },
            description: { type: 'string', default: 'StarApple is geen standaard bemiddelaar. Bij ons draait het om de perfecte interim match. Wij combineren inhoudelijke IT-expertise met een persoonlijke, gedreven aanpak. Of je nu zoekt naar de juiste interim opdracht of tijdelijke versterking, bij ons weet je precies wie je aan de lijn hebt en waar je aan toe bent. Leer ons kennen.' },
            buttonText: { type: 'string', default: 'Over ons' },
            buttonUrl: { type: 'string', default: '#' },
            buttonTarget: { type: 'string', default: '' },
            buttonRel: { type: 'string', default: '' },
            showButton: { type: 'boolean', default: true },
            buttonBackgroundType: { type: 'string', default: 'primary', enum: ['primary', 'secondary', 'custom'] },
            customButtonColor: { type: 'string', default: '#9333ea' },
            imageUrl: { type: 'string', default: 'https://images.unsplash.com/photo-1621155346337-1d19476ba7d6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHw0fHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1080&w=1200&h=800&fit=crop' },
            imageAlt: { type: 'string', default: 'Team collaboration' },
            imageId: { type: 'number', default: 0 },
            imagePosition: { type: 'string', default: 'right', enum: ['left', 'right'] },
            backgroundColor: { type: 'string', default: '#f9fafb' },
            textColor: { type: 'string', default: '#1f2937' },
            smallHeadingColor: { type: 'string', default: '#4b5563' },
            descriptionColor: { type: 'string', default: '#374151' },
            contentAlignment: { type: 'string', default: 'left', enum: ['left', 'center'] },
            imageRoundness: { type: 'string', default: '3xl', enum: ['none', 'lg', 'xl', '2xl', '3xl', 'full'] },
            columnGap: { type: 'string', default: '12', enum: ['8', '10', '12', '16'] },
            verticalPadding: { type: 'string', default: '12', enum: ['8', '12', '16', '20'] },
            showShadow: { type: 'boolean', default: true }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                smallHeading,
                mainHeading, 
                description, 
                buttonText, 
                buttonUrl, 
                buttonTarget, 
                buttonRel, 
                showButton, 
                buttonBackgroundType, 
                customButtonColor,
                imageUrl,
                imageAlt,
                imageId,
                imagePosition,
                backgroundColor,
                textColor,
                smallHeadingColor,
                descriptionColor,
                contentAlignment,
                imageRoundness,
                columnGap,
                verticalPadding,
                showShadow
            } = attributes;

            const [showLinkControl, setShowLinkControl] = useState(false);

            // SOPHISTICATED helper functions for dynamic styling
            const getButtonBackgroundColor = () => {
                switch(buttonBackgroundType) {
                    case 'primary': 
                        return 'var(--wp--preset--color--primary, #9333ea)';
                    case 'secondary': 
                        return 'var(--wp--preset--color--secondary, #84eb93)';
                    case 'custom': 
                        return customButtonColor;
                    default: 
                        return '#9333ea';
                }
            };

            const getButtonTextColor = () => {
                if (buttonBackgroundType === 'secondary') {
                    return '#1f2937';
                }
                return buttonBackgroundType === 'custom' ? getContrastColor(customButtonColor) : '#ffffff';
            };

            const getContrastColor = (hexColor) => {
                const hex = hexColor.replace('#', '');
                const r = parseInt(hex.substr(0, 2), 16);
                const g = parseInt(hex.substr(2, 2), 16);
                const b = parseInt(hex.substr(4, 2), 16);
                const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                return brightness > 128 ? '#1f2937' : '#ffffff';
            };

            const getRoundnessClass = () => {
                const roundnessMap = {
                    'none': 'rounded-none',
                    'lg': 'rounded-lg',
                    'xl': 'rounded-xl',
                    '2xl': 'rounded-2xl',
                    '3xl': 'rounded-3xl',
                    'full': 'rounded-full'
                };
                return roundnessMap[imageRoundness] || 'rounded-3xl';
            };

            const onSelectImage = (media) => {
                setAttributes({
                    imageUrl: media.url,
                    imageId: media.id,
                    imageAlt: media.alt || 'Image'
                });
            };

            const onRemoveImage = () => {
                setAttributes({
                    imageUrl: '',
                    imageId: 0,
                    imageAlt: ''
                });
            };

            // ADVANCED editor preview with realistic styling
            const textColumn = createElement('div', { 
                className: 'flex flex-col justify-center space-y-6',
                style: { textAlign: contentAlignment }
            },
                createElement('div', null,
                    createElement(RichText, {
                        tagName: 'p',
                        className: 'text-base sm:text-lg font-normal',
                        style: { color: smallHeadingColor },
                        value: smallHeading,
                        onChange: (value) => setAttributes({ smallHeading: value }),
                        placeholder: __('Enter small heading...', 'julianboelen')
                    })
                ),
                
                createElement('div', null,
                    createElement(RichText, {
                        tagName: 'h1',
                        className: 'text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight',
                        style: { color: textColor },
                        value: mainHeading,
                        onChange: (value) => setAttributes({ mainHeading: value }),
                        placeholder: __('Enter main heading...', 'julianboelen')
                    })
                ),
                
                createElement('div', null,
                    createElement(RichText, {
                        tagName: 'p',
                        className: 'text-base sm:text-lg leading-relaxed',
                        style: { color: descriptionColor },
                        value: description,
                        onChange: (value) => setAttributes({ description: value }),
                        placeholder: __('Enter description...', 'julianboelen')
                    })
                ),
                
                showButton && createElement('div', { className: 'pt-2' },
                    createElement('div', {
                        className: 'inline-block px-10 py-4 rounded-full font-semibold text-base sm:text-lg shadow-md transition-all duration-300 cursor-pointer',
                        style: {
                            backgroundColor: getButtonBackgroundColor(),
                            color: getButtonTextColor()
                        },
                        contentEditable: true,
                        suppressContentEditableWarning: true,
                        onBlur: (e) => setAttributes({ buttonText: e.target.textContent })
                    }, buttonText)
                )
            );

            const imageColumn = createElement('div', { className: 'flex justify-center lg:justify-end' },
                createElement('div', { className: 'w-full max-w-2xl' },
                    createElement(MediaUploadCheck, null,
                        createElement(MediaUpload, {
                            onSelect: onSelectImage,
                            allowedTypes: ['image'],
                            value: imageId,
                            render: ({ open }) => {
                                return imageUrl ? 
                                    createElement('div', { 
                                        className: 'relative group',
                                        style: { cursor: 'pointer' }
                                    },
                                        createElement('img', {
                                            src: imageUrl,
                                            alt: imageAlt,
                                            className: `w-full h-auto object-cover ${getRoundnessClass()} ${showShadow ? 'shadow-lg' : ''}`,
                                            onClick: open,
                                            style: { maxHeight: '500px' }
                                        }),
                                        createElement('div', {
                                            className: 'absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity'
                                        },
                                            createElement(Button, {
                                                onClick: onRemoveImage,
                                                isDestructive: true,
                                                isSmall: true
                                            }, __('Remove', 'julianboelen'))
                                        )
                                    ) :
                                    createElement('div', {
                                        className: `w-full h-64 flex items-center justify-center bg-gray-200 ${getRoundnessClass()} cursor-pointer hover:bg-gray-300 transition-colors`,
                                        onClick: open
                                    },
                                        createElement('span', { className: 'text-gray-500' }, __('Upload Image', 'julianboelen'))
                                    );
                            }
                        })
                    )
                )
            );

            return createElement(Fragment, null,
                // SOPHISTICATED InspectorControls with multiple panels
                createElement(InspectorControls, null,
                    createElement(PanelBody, { 
                        title: __('Content Settings', 'julianboelen'), 
                        initialOpen: true 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(TextControl, {
                                    label: __('Small Heading', 'julianboelen'),
                                    value: smallHeading,
                                    onChange: (value) => setAttributes({ smallHeading: value }),
                                    help: __('Text displayed above the main heading', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(TextControl, {
                                    label: __('Image Alt Text', 'julianboelen'),
                                    value: imageAlt,
                                    onChange: (value) => setAttributes({ imageAlt: value }),
                                    help: __('Describe the image for accessibility', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Content Alignment', 'julianboelen'),
                                    value: contentAlignment,
                                    options: [
                                        { label: __('Left', 'julianboelen'), value: 'left' },
                                        { label: __('Center', 'julianboelen'), value: 'center' }
                                    ],
                                    onChange: (value) => setAttributes({ contentAlignment: value })
                                })
                            )
                        )
                    ),
                    
                    createElement(PanelBody, { 
                        title: __('Layout Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Image Position', 'julianboelen'),
                                    value: imagePosition,
                                    options: [
                                        { label: __('Right', 'julianboelen'), value: 'right' },
                                        { label: __('Left', 'julianboelen'), value: 'left' }
                                    ],
                                    onChange: (value) => setAttributes({ imagePosition: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Column Gap', 'julianboelen'),
                                    value: columnGap,
                                    options: [
                                        { label: __('Small (2rem)', 'julianboelen'), value: '8' },
                                        { label: __('Medium (2.5rem)', 'julianboelen'), value: '10' },
                                        { label: __('Large (3rem)', 'julianboelen'), value: '12' },
                                        { label: __('Extra Large (4rem)', 'julianboelen'), value: '16' }
                                    ],
                                    onChange: (value) => setAttributes({ columnGap: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Vertical Padding', 'julianboelen'),
                                    value: verticalPadding,
                                    options: [
                                        { label: __('Small (2rem)', 'julianboelen'), value: '8' },
                                        { label: __('Medium (3rem)', 'julianboelen'), value: '12' },
                                        { label: __('Large (4rem)', 'julianboelen'), value: '16' },
                                        { label: __('Extra Large (5rem)', 'julianboelen'), value: '20' }
                                    ],
                                    onChange: (value) => setAttributes({ verticalPadding: value })
                                })
                            )
                        )
                    ),
                    
                    createElement(PanelBody, { 
                        title: __('Design Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(BaseControl, {
                                    label: __('Background Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: backgroundColor,
                                        onChange: (color) => setAttributes({ backgroundColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Main Heading Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: textColor,
                                        onChange: (color) => setAttributes({ textColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Small Heading Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: smallHeadingColor,
                                        onChange: (color) => setAttributes({ smallHeadingColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Description Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: descriptionColor,
                                        onChange: (color) => setAttributes({ descriptionColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(SelectControl, {
                                    label: __('Image Roundness', 'julianboelen'),
                                    value: imageRoundness,
                                    options: [
                                        { label: __('None', 'julianboelen'), value: 'none' },
                                        { label: __('Large', 'julianboelen'), value: 'lg' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xl' },
                                        { label: __('2X Large', 'julianboelen'), value: '2xl' },
                                        { label: __('3X Large', 'julianboelen'), value: '3xl' },
                                        { label: __('Full', 'julianboelen'), value: 'full' }
                                    ],
                                    onChange: (value) => setAttributes({ imageRoundness: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement(ToggleControl, {
                                label: __('Show Image Shadow', 'julianboelen'),
                                checked: showShadow,
                                onChange: (value) => setAttributes({ showShadow: value })
                            })
                        )
                    ),
                    
                    createElement(PanelBody, { 
                        title: __('Button Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement(ToggleControl, {
                                label: __('Show Button', 'julianboelen'),
                                checked: showButton,
                                onChange: (value) => setAttributes({ showButton: value })
                            })
                        ),
                        showButton && createElement(Fragment, null,
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(TextControl, {
                                        label: __('Button Text', 'julianboelen'),
                                        value: buttonText,
                                        onChange: (value) => setAttributes({ buttonText: value })
                                    })
                                )
                            ),
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(TextControl, {
                                        label: __('Button URL', 'julianboelen'),
                                        value: buttonUrl,
                                        onChange: (value) => setAttributes({ buttonUrl: value }),
                                        type: 'url'
                                    })
                                )
                            ),
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(ToggleControl, {
                                        label: __('Open in New Tab', 'julianboelen'),
                                        checked: buttonTarget === '_blank',
                                        onChange: (value) => {
                                            setAttributes({
                                                buttonTarget: value ? '_blank' : '',
                                                buttonRel: value ? 'noopener noreferrer' : ''
                                            });
                                        }
                                    })
                                )
                            ),
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(SelectControl, {
                                        label: __('Button Background', 'julianboelen'),
                                        value: buttonBackgroundType,
                                        options: [
                                            { label: __('Primary', 'julianboelen'), value: 'primary' },
                                            { label: __('Secondary', 'julianboelen'), value: 'secondary' },
                                            { label: __('Custom', 'julianboelen'), value: 'custom' }
                                        ],
                                        onChange: (value) => setAttributes({ buttonBackgroundType: value })
                                    })
                                )
                            ),
                            buttonBackgroundType === 'custom' && createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(BaseControl, {
                                        label: __('Custom Button Color', 'julianboelen')
                                    },
                                        createElement(ColorPicker, {
                                            color: customButtonColor,
                                            onChange: (color) => setAttributes({ customButtonColor: color.hex }),
                                            disableAlpha: false
                                        })
                                    )
                                )
                            )
                        )
                    )
                ),
                
                // PROFESSIONAL editor preview with realistic styling
                createElement('section', { 
                    ...useBlockProps({
                        className: `section-text-image-block-preview py-${verticalPadding} px-4 sm:px-6 lg:px-8`,
                        style: { 
                            backgroundColor: backgroundColor,
                            border: '2px dashed #ccc',
                            borderRadius: '8px'
                        }
                    })
                },
                    createElement('div', { className: 'max-w-7xl mx-auto' },
                        createElement('div', { 
                            className: `grid grid-cols-1 lg:grid-cols-2 gap-${columnGap} items-center`,
                            style: {
                                gridTemplateColumns: imagePosition === 'left' ? 'auto' : '1fr 1fr'
                            }
                        },
                            imagePosition === 'left' ? [
                                createElement('div', { key: 'image', className: 'order-1 lg:order-1' }, imageColumn),
                                createElement('div', { key: 'text', className: 'order-2 lg:order-2' }, textColumn)
                            ] : [
                                createElement('div', { key: 'text', className: 'order-2 lg:order-1' }, textColumn),
                                createElement('div', { key: 'image', className: 'order-1 lg:order-2' }, imageColumn)
                            ]
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