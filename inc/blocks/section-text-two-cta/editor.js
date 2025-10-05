(function() {
    const { registerBlockType } = wp.blocks;
    const { RichText, InspectorControls, useBlockProps, LinkControl } = wp.blockEditor;
    const { Button, PanelBody, PanelRow, SelectControl, ColorPicker, TextControl, BaseControl, TextareaControl } = wp.components;
    const { Fragment, createElement, useState } = wp.element;
    const { __ } = wp.i18n;

    registerBlockType('julianboelen/section-text-two-cta', {
        apiVersion: 2,
        title: __('Section Text Two CTA', 'julianboelen'),
        icon: 'layout',
        category: 'julianboelen-blocks',
        description: __('A sophisticated three-column layout with about section and two CTA cards', 'julianboelen'),
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
            aboutTitle: { type: 'string', default: 'Over ons' },
            aboutParagraph1: { type: 'string', default: 'We koppelen IT-specialisten die op zoek zijn naar een nieuwe uitdaging of een volgende stap in hun carrière aan ambitieuze bedrijven. Dat doen we sinds 2008, en inmiddels hebben we stevig naam gemaakt binnen de Nederlandse IT-markt.' },
            aboutParagraph2: { type: 'string', default: 'We werken vanuit een mooie plek in Den Haag met een bevlogen team van servicegerichte recruitment consultants die elk een eigen expertiseveld en regio hebben. Van daaruit begeleiden ze kandidaten in het traject richting een nieuwe uitdaging.' },
            aboutParagraph3: { type: 'string', default: 'Inmiddels hebben we al meer dan 5000 specialisten succesvol kunnen verbinden aan uiteenlopende organisaties. En we zijn nog lang niet klaar.' },
            aboutBackgroundColor: { type: 'string', default: '#f9fafb' },
            aboutTextColor: { type: 'string', default: '#374151' },
            card1Title: { type: 'string', default: 'Voor IT-professionals' },
            card1Description: { type: 'string', default: 'Ben je freelance IT\'er of op zoek naar een nieuwe interim opdracht? Bij StarApple vind je uitdagende projecten die bij je passen — bij toonaangevende opdrachtgevers in overheid, corporate en tech.' },
            card1Url: { type: 'string', default: '#' },
            card1Target: { type: 'string', default: '' },
            card1Rel: { type: 'string', default: '' },
            card1GradientFrom: { type: 'string', default: '#a855f7' },
            card1GradientTo: { type: 'string', default: '#9333ea' },
            card1TextColor: { type: 'string', default: '#ffffff' },
            card1ButtonColor: { type: 'string', default: '#9333ea' },
            card2Title: { type: 'string', default: 'Voor opdrachtgevers' },
            card2Description: { type: 'string', default: 'Op zoek naar tijdelijke IT-versterking of een expert voor jouw project? StarApple levert snel de juiste professional — met diepgaande marktkennis en een persoonlijke aanpak.' },
            card2Url: { type: 'string', default: '#' },
            card2Target: { type: 'string', default: '' },
            card2Rel: { type: 'string', default: '' },
            card2GradientFrom: { type: 'string', default: '#4ade80' },
            card2GradientTo: { type: 'string', default: '#22c55e' },
            card2TextColor: { type: 'string', default: '#111827' },
            card2ButtonColor: { type: 'string', default: '#16a34a' },
            containerMaxWidth: { type: 'string', default: '7xl' },
            verticalPadding: { type: 'string', default: 'default' },
            cardGap: { type: 'string', default: 'default' },
            borderRadius: { type: 'string', default: '3xl' }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                aboutTitle,
                aboutParagraph1,
                aboutParagraph2,
                aboutParagraph3,
                aboutBackgroundColor,
                aboutTextColor,
                card1Title,
                card1Description,
                card1Url,
                card1Target,
                card1Rel,
                card1GradientFrom,
                card1GradientTo,
                card1TextColor,
                card1ButtonColor,
                card2Title,
                card2Description,
                card2Url,
                card2Target,
                card2Rel,
                card2GradientFrom,
                card2GradientTo,
                card2TextColor,
                card2ButtonColor,
                containerMaxWidth,
                verticalPadding,
                cardGap,
                borderRadius
            } = attributes;

            const [showCard1LinkControl, setShowCard1LinkControl] = useState(false);
            const [showCard2LinkControl, setShowCard2LinkControl] = useState(false);
            const [card1LinkValue, setCard1LinkValue] = useState({
                url: card1Url,
                opensInNewTab: card1Target === '_blank'
            });
            const [card2LinkValue, setCard2LinkValue] = useState({
                url: card2Url,
                opensInNewTab: card2Target === '_blank'
            });

            // SOPHISTICATED helper functions
            const getContrastColor = (hexColor) => {
                const hex = hexColor.replace('#', '');
                const r = parseInt(hex.substr(0, 2), 16);
                const g = parseInt(hex.substr(2, 2), 16);
                const b = parseInt(hex.substr(4, 2), 16);
                const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                return brightness > 128 ? '#1f2937' : '#ffffff';
            };

            const getMaxWidthClass = () => {
                const widths = {
                    '5xl': 'max-w-5xl',
                    '6xl': 'max-w-6xl',
                    '7xl': 'max-w-7xl',
                    'full': 'max-w-full'
                };
                return widths[containerMaxWidth] || 'max-w-7xl';
            };

            const getPaddingClass = () => {
                const paddings = {
                    'small': 'py-6 sm:py-8',
                    'default': 'py-8 sm:py-12',
                    'large': 'py-12 sm:py-16',
                    'xlarge': 'py-16 sm:py-20'
                };
                return paddings[verticalPadding] || 'py-8 sm:py-12';
            };

            const getGapClass = () => {
                const gaps = {
                    'small': 'gap-3 lg:gap-4',
                    'default': 'gap-4 lg:gap-6',
                    'large': 'gap-6 lg:gap-8'
                };
                return gaps[cardGap] || 'gap-4 lg:gap-6';
            };

            const getBorderRadiusClass = () => {
                const radii = {
                    'lg': 'rounded-lg',
                    'xl': 'rounded-xl',
                    '2xl': 'rounded-2xl',
                    '3xl': 'rounded-3xl'
                };
                return radii[borderRadius] || 'rounded-3xl';
            };

            const handleCard1LinkChange = (newLink) => {
                setCard1LinkValue(newLink);
                setAttributes({
                    card1Url: newLink.url,
                    card1Target: newLink.opensInNewTab ? '_blank' : '',
                    card1Rel: newLink.opensInNewTab ? 'noopener' : ''
                });
            };

            const handleCard2LinkChange = (newLink) => {
                setCard2LinkValue(newLink);
                setAttributes({
                    card2Url: newLink.url,
                    card2Target: newLink.opensInNewTab ? '_blank' : '',
                    card2Rel: newLink.opensInNewTab ? 'noopener' : ''
                });
            };

            // ADVANCED editor preview
            return createElement(Fragment, null,
                // SOPHISTICATED InspectorControls
                createElement(InspectorControls, null,
                    // About Section Settings
                    createElement(PanelBody, { 
                        title: __('About Section Settings', 'julianboelen'), 
                        initialOpen: true 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(TextControl, {
                                    label: __('About Title', 'julianboelen'),
                                    value: aboutTitle,
                                    onChange: (value) => setAttributes({ aboutTitle: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(BaseControl, {
                                    label: __('About Background Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: aboutBackgroundColor,
                                        onChange: (color) => setAttributes({ aboutBackgroundColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('About Text Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: aboutTextColor,
                                        onChange: (color) => setAttributes({ aboutTextColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        )
                    ),
                    
                    // Card 1 Settings
                    createElement(PanelBody, { 
                        title: __('Card 1 Settings (Purple)', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(TextControl, {
                                    label: __('Card 1 Title', 'julianboelen'),
                                    value: card1Title,
                                    onChange: (value) => setAttributes({ card1Title: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(TextareaControl, {
                                    label: __('Card 1 Description', 'julianboelen'),
                                    value: card1Description,
                                    onChange: (value) => setAttributes({ card1Description: value }),
                                    rows: 4
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(BaseControl, {
                                    label: __('Gradient From Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: card1GradientFrom,
                                        onChange: (color) => setAttributes({ card1GradientFrom: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Gradient To Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: card1GradientTo,
                                        onChange: (color) => setAttributes({ card1GradientTo: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Text Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: card1TextColor,
                                        onChange: (color) => setAttributes({ card1TextColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Button Icon Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: card1ButtonColor,
                                        onChange: (color) => setAttributes({ card1ButtonColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(Button, {
                                    variant: showCard1LinkControl ? 'secondary' : 'primary',
                                    onClick: () => setShowCard1LinkControl(!showCard1LinkControl)
                                }, showCard1LinkControl ? __('Hide Link Settings', 'julianboelen') : __('Edit Link', 'julianboelen')),
                                showCard1LinkControl && createElement('div', { style: { marginTop: '10px' } },
                                    createElement(LinkControl, {
                                        value: card1LinkValue,
                                        onChange: handleCard1LinkChange
                                    })
                                )
                            )
                        )
                    ),
                    
                    // Card 2 Settings
                    createElement(PanelBody, { 
                        title: __('Card 2 Settings (Green)', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(TextControl, {
                                    label: __('Card 2 Title', 'julianboelen'),
                                    value: card2Title,
                                    onChange: (value) => setAttributes({ card2Title: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(TextareaControl, {
                                    label: __('Card 2 Description', 'julianboelen'),
                                    value: card2Description,
                                    onChange: (value) => setAttributes({ card2Description: value }),
                                    rows: 4
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(BaseControl, {
                                    label: __('Gradient From Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: card2GradientFrom,
                                        onChange: (color) => setAttributes({ card2GradientFrom: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Gradient To Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: card2GradientTo,
                                        onChange: (color) => setAttributes({ card2GradientTo: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Text Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: card2TextColor,
                                        onChange: (color) => setAttributes({ card2TextColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Button Icon Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: card2ButtonColor,
                                        onChange: (color) => setAttributes({ card2ButtonColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(Button, {
                                    variant: showCard2LinkControl ? 'secondary' : 'primary',
                                    onClick: () => setShowCard2LinkControl(!showCard2LinkControl)
                                }, showCard2LinkControl ? __('Hide Link Settings', 'julianboelen') : __('Edit Link', 'julianboelen')),
                                showCard2LinkControl && createElement('div', { style: { marginTop: '10px' } },
                                    createElement(LinkControl, {
                                        value: card2LinkValue,
                                        onChange: handleCard2LinkChange
                                    })
                                )
                            )
                        )
                    ),
                    
                    // Layout Settings
                    createElement(PanelBody, { 
                        title: __('Layout Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Container Max Width', 'julianboelen'),
                                    value: containerMaxWidth,
                                    options: [
                                        { label: __('5XL (1024px)', 'julianboelen'), value: '5xl' },
                                        { label: __('6XL (1152px)', 'julianboelen'), value: '6xl' },
                                        { label: __('7XL (1280px)', 'julianboelen'), value: '7xl' },
                                        { label: __('Full Width', 'julianboelen'), value: 'full' }
                                    ],
                                    onChange: (value) => setAttributes({ containerMaxWidth: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Vertical Padding', 'julianboelen'),
                                    value: verticalPadding,
                                    options: [
                                        { label: __('Small', 'julianboelen'), value: 'small' },
                                        { label: __('Default', 'julianboelen'), value: 'default' },
                                        { label: __('Large', 'julianboelen'), value: 'large' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xlarge' }
                                    ],
                                    onChange: (value) => setAttributes({ verticalPadding: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Card Gap', 'julianboelen'),
                                    value: cardGap,
                                    options: [
                                        { label: __('Small', 'julianboelen'), value: 'small' },
                                        { label: __('Default', 'julianboelen'), value: 'default' },
                                        { label: __('Large', 'julianboelen'), value: 'large' }
                                    ],
                                    onChange: (value) => setAttributes({ cardGap: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Border Radius', 'julianboelen'),
                                    value: borderRadius,
                                    options: [
                                        { label: __('Large', 'julianboelen'), value: 'lg' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xl' },
                                        { label: __('2XL', 'julianboelen'), value: '2xl' },
                                        { label: __('3XL', 'julianboelen'), value: '3xl' }
                                    ],
                                    onChange: (value) => setAttributes({ borderRadius: value })
                                })
                            )
                        )
                    )
                ),
                
                // PROFESSIONAL editor preview
                createElement('div', { 
                    ...useBlockProps({
                        className: 'section-text-two-cta-editor-preview',
                        style: {
                            border: '2px dashed #ccc',
                            borderRadius: '8px',
                            padding: '20px',
                            backgroundColor: '#f5f5f5'
                        }
                    })
                },
                    createElement('div', { 
                        className: `w-full ${getMaxWidthClass()} mx-auto px-4 ${getPaddingClass()}`
                    },
                        createElement('div', { 
                            className: `grid grid-cols-1 lg:grid-cols-2 ${getGapClass()}`
                        },
                            // Left Column - About Us
                            createElement('div', { 
                                className: `${getBorderRadiusClass()} p-8 sm:p-10 lg:p-12 shadow-sm`,
                                style: {
                                    backgroundColor: aboutBackgroundColor,
                                    color: aboutTextColor
                                }
                            },
                                createElement(RichText, {
                                    tagName: 'h2',
                                    className: 'text-3xl sm:text-4xl font-bold mb-6',
                                    value: aboutTitle,
                                    onChange: (value) => setAttributes({ aboutTitle: value }),
                                    placeholder: __('Enter about title...', 'julianboelen')
                                }),
                                createElement('div', { className: 'space-y-6 leading-relaxed' },
                                    createElement(RichText, {
                                        tagName: 'p',
                                        value: aboutParagraph1,
                                        onChange: (value) => setAttributes({ aboutParagraph1: value }),
                                        placeholder: __('Enter first paragraph...', 'julianboelen')
                                    }),
                                    createElement(RichText, {
                                        tagName: 'p',
                                        value: aboutParagraph2,
                                        onChange: (value) => setAttributes({ aboutParagraph2: value }),
                                        placeholder: __('Enter second paragraph...', 'julianboelen')
                                    }),
                                    createElement(RichText, {
                                        tagName: 'p',
                                        value: aboutParagraph3,
                                        onChange: (value) => setAttributes({ aboutParagraph3: value }),
                                        placeholder: __('Enter third paragraph...', 'julianboelen')
                                    })
                                )
                            ),
                            
                            // Right Column - Two Stacked Cards
                            createElement('div', { className: `grid grid-cols-1 ${getGapClass()}` },
                                // Card 1 - IT Professionals
                                createElement('div', { 
                                    className: `${getBorderRadiusClass()} p-8 sm:p-10 lg:p-12 shadow-sm relative`,
                                    style: {
                                        background: `linear-gradient(to bottom right, ${card1GradientFrom}, ${card1GradientTo})`,
                                        color: card1TextColor
                                    }
                                },
                                    createElement(RichText, {
                                        tagName: 'h2',
                                        className: 'text-3xl sm:text-4xl font-bold mb-6',
                                        value: card1Title,
                                        onChange: (value) => setAttributes({ card1Title: value }),
                                        placeholder: __('Enter card 1 title...', 'julianboelen')
                                    }),
                                    createElement(RichText, {
                                        tagName: 'p',
                                        className: 'text-base sm:text-lg leading-relaxed mb-4',
                                        value: card1Description,
                                        onChange: (value) => setAttributes({ card1Description: value }),
                                        placeholder: __('Enter card 1 description...', 'julianboelen')
                                    }),
                                    createElement('button', {
                                        className: 'absolute bottom-8 right-8 w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow',
                                        style: { cursor: 'pointer' }
                                    },
                                        createElement('svg', {
                                            className: 'w-6 h-6',
                                            fill: 'none',
                                            stroke: card1ButtonColor,
                                            viewBox: '0 0 24 24',
                                            style: { strokeWidth: '2.5' }
                                        },
                                            createElement('path', {
                                                strokeLinecap: 'round',
                                                strokeLinejoin: 'round',
                                                d: 'M17 8l4 4m0 0l-4 4m4-4H3'
                                            })
                                        )
                                    )
                                ),
                                
                                // Card 2 - Opdrachtgevers
                                createElement('div', { 
                                    className: `${getBorderRadiusClass()} p-8 sm:p-10 lg:p-12 shadow-sm relative`,
                                    style: {
                                        background: `linear-gradient(to bottom right, ${card2GradientFrom}, ${card2GradientTo})`,
                                        color: card2TextColor
                                    }
                                },
                                    createElement(RichText, {
                                        tagName: 'h2',
                                        className: 'text-3xl sm:text-4xl font-bold mb-6',
                                        value: card2Title,
                                        onChange: (value) => setAttributes({ card2Title: value }),
                                        placeholder: __('Enter card 2 title...', 'julianboelen')
                                    }),
                                    createElement(RichText, {
                                        tagName: 'p',
                                        className: 'text-base sm:text-lg leading-relaxed mb-4',
                                        value: card2Description,
                                        onChange: (value) => setAttributes({ card2Description: value }),
                                        placeholder: __('Enter card 2 description...', 'julianboelen')
                                    }),
                                    createElement('button', {
                                        className: 'absolute bottom-8 right-8 w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow',
                                        style: { cursor: 'pointer' }
                                    },
                                        createElement('svg', {
                                            className: 'w-6 h-6',
                                            fill: 'none',
                                            stroke: card2ButtonColor,
                                            viewBox: '0 0 24 24',
                                            style: { strokeWidth: '2.5' }
                                        },
                                            createElement('path', {
                                                strokeLinecap: 'round',
                                                strokeLinejoin: 'round',
                                                d: 'M17 8l4 4m0 0l-4 4m4-4H3'
                                            })
                                        )
                                    )
                                )
                            )
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