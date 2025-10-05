(function() {
    const { registerBlockType } = wp.blocks;
    const { RichText, InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, PanelRow, ToggleControl, SelectControl, RangeControl, BaseControl, ColorPicker } = wp.components;
    const { Fragment, createElement } = wp.element;
    const { __ } = wp.i18n;

    registerBlockType('julianboelen/section-title-text-horizontal', {
        apiVersion: 2,
        title: __('Section Title Text Horizontal', 'julianboelen'),
        icon: 'columns',
        category: 'julianboelen-blocks',
        description: __('A sophisticated two-column layout with heading on the left and body text on the right', 'julianboelen'),
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
            },
            color: {
                background: true,
                text: true
            }
        },
        
        attributes: {
            heading: {
                type: 'string',
                default: 'Wij zijn Starapple en wij willen talent laten groeien!'
            },
            paragraph1: {
                type: 'string',
                default: 'Een brug slaan tussen aanbod van en vraag naar uiterst specifieke IT-specialisten, dat is Starapple in één zin.'
            },
            paragraph2: {
                type: 'string',
                default: 'Wij helpen je graag in jou zoektocht naar een nieuwe uitdaging zodat jij jouw carrière verder kunt ontwikkelen.'
            },
            backgroundColor: {
                type: 'string',
                default: '#ffffff'
            },
            headingColor: {
                type: 'string',
                default: '#111827'
            },
            textColor: {
                type: 'string',
                default: '#111827'
            },
            headingSize: {
                type: 'string',
                default: 'large',
                enum: ['medium', 'large', 'xlarge']
            },
            textSize: {
                type: 'string',
                default: 'base',
                enum: ['small', 'base', 'large']
            },
            columnRatio: {
                type: 'string',
                default: '5-7',
                enum: ['4-8', '5-7', '6-6']
            },
            verticalAlignment: {
                type: 'string',
                default: 'start',
                enum: ['start', 'center', 'end']
            },
            columnGap: {
                type: 'string',
                default: 'large',
                enum: ['small', 'medium', 'large', 'xlarge']
            },
            paddingTop: {
                type: 'string',
                default: '16'
            },
            paddingBottom: {
                type: 'string',
                default: '16'
            },
            maxWidth: {
                type: 'string',
                default: '7xl',
                enum: ['5xl', '6xl', '7xl', 'full']
            },
            showParagraph2: {
                type: 'boolean',
                default: true
            },
            paragraphSpacing: {
                type: 'string',
                default: '6'
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                heading,
                paragraph1,
                paragraph2,
                backgroundColor,
                headingColor,
                textColor,
                headingSize,
                textSize,
                columnRatio,
                verticalAlignment,
                columnGap,
                paddingTop,
                paddingBottom,
                maxWidth,
                showParagraph2,
                paragraphSpacing
            } = attributes;

            // SOPHISTICATED helper functions for dynamic styling
            const getHeadingSizeClass = () => {
                switch(headingSize) {
                    case 'medium':
                        return 'text-3xl sm:text-4xl lg:text-5xl';
                    case 'large':
                        return 'text-4xl sm:text-5xl lg:text-6xl';
                    case 'xlarge':
                        return 'text-5xl sm:text-6xl lg:text-7xl';
                    default:
                        return 'text-4xl sm:text-5xl lg:text-6xl';
                }
            };

            const getTextSizeClass = () => {
                switch(textSize) {
                    case 'small':
                        return 'text-sm sm:text-base';
                    case 'base':
                        return 'text-base sm:text-lg';
                    case 'large':
                        return 'text-lg sm:text-xl';
                    default:
                        return 'text-base sm:text-lg';
                }
            };

            const getColumnRatioClasses = () => {
                switch(columnRatio) {
                    case '4-8':
                        return { left: 'lg:col-span-4', right: 'lg:col-span-8' };
                    case '5-7':
                        return { left: 'lg:col-span-5', right: 'lg:col-span-7' };
                    case '6-6':
                        return { left: 'lg:col-span-6', right: 'lg:col-span-6' };
                    default:
                        return { left: 'lg:col-span-5', right: 'lg:col-span-7' };
                }
            };

            const getAlignmentClass = () => {
                switch(verticalAlignment) {
                    case 'start':
                        return 'items-start';
                    case 'center':
                        return 'items-center';
                    case 'end':
                        return 'items-end';
                    default:
                        return 'items-start';
                }
            };

            const getGapClass = () => {
                switch(columnGap) {
                    case 'small':
                        return 'gap-4 lg:gap-6';
                    case 'medium':
                        return 'gap-6 lg:gap-8';
                    case 'large':
                        return 'gap-8 lg:gap-12';
                    case 'xlarge':
                        return 'gap-10 lg:gap-16';
                    default:
                        return 'gap-8 lg:gap-12';
                }
            };

            const getMaxWidthClass = () => {
                switch(maxWidth) {
                    case '5xl':
                        return 'max-w-5xl';
                    case '6xl':
                        return 'max-w-6xl';
                    case '7xl':
                        return 'max-w-7xl';
                    case 'full':
                        return 'max-w-full';
                    default:
                        return 'max-w-7xl';
                }
            };

            const getPaddingStyle = () => {
                return {
                    paddingTop: `${paddingTop}px`,
                    paddingBottom: `${paddingBottom}px`
                };
            };

            const columnClasses = getColumnRatioClasses();
            const headingSizeClass = getHeadingSizeClass();
            const textSizeClass = getTextSizeClass();
            const alignmentClass = getAlignmentClass();
            const gapClass = getGapClass();
            const maxWidthClass = getMaxWidthClass();

            // ADVANCED editor preview with realistic styling
            return createElement(Fragment, null,
                // SOPHISTICATED InspectorControls with multiple panels
                createElement(InspectorControls, null,
                    createElement(PanelBody, { 
                        title: __('Content Settings', 'julianboelen'), 
                        initialOpen: true 
                    },
                        createElement(PanelRow, null,
                            createElement(ToggleControl, {
                                label: __('Show Second Paragraph', 'julianboelen'),
                                checked: showParagraph2,
                                onChange: (value) => setAttributes({ showParagraph2: value }),
                                help: __('Toggle visibility of the second paragraph', 'julianboelen')
                            })
                        ),
                        showParagraph2 && createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(RangeControl, {
                                    label: __('Paragraph Spacing', 'julianboelen'),
                                    value: parseInt(paragraphSpacing),
                                    onChange: (value) => setAttributes({ paragraphSpacing: value.toString() }),
                                    min: 2,
                                    max: 12,
                                    step: 1,
                                    help: __('Space between paragraphs in pixels', 'julianboelen')
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
                                    label: __('Column Ratio', 'julianboelen'),
                                    value: columnRatio,
                                    options: [
                                        { label: __('4:8 (Narrow Left)', 'julianboelen'), value: '4-8' },
                                        { label: __('5:7 (Balanced)', 'julianboelen'), value: '5-7' },
                                        { label: __('6:6 (Equal)', 'julianboelen'), value: '6-6' }
                                    ],
                                    onChange: (value) => setAttributes({ columnRatio: value }),
                                    help: __('Adjust the width ratio between columns', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Vertical Alignment', 'julianboelen'),
                                    value: verticalAlignment,
                                    options: [
                                        { label: __('Top', 'julianboelen'), value: 'start' },
                                        { label: __('Center', 'julianboelen'), value: 'center' },
                                        { label: __('Bottom', 'julianboelen'), value: 'end' }
                                    ],
                                    onChange: (value) => setAttributes({ verticalAlignment: value }),
                                    help: __('Vertical alignment of columns', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Column Gap', 'julianboelen'),
                                    value: columnGap,
                                    options: [
                                        { label: __('Small', 'julianboelen'), value: 'small' },
                                        { label: __('Medium', 'julianboelen'), value: 'medium' },
                                        { label: __('Large', 'julianboelen'), value: 'large' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xlarge' }
                                    ],
                                    onChange: (value) => setAttributes({ columnGap: value }),
                                    help: __('Space between columns', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Maximum Width', 'julianboelen'),
                                    value: maxWidth,
                                    options: [
                                        { label: __('5XL (1024px)', 'julianboelen'), value: '5xl' },
                                        { label: __('6XL (1152px)', 'julianboelen'), value: '6xl' },
                                        { label: __('7XL (1280px)', 'julianboelen'), value: '7xl' },
                                        { label: __('Full Width', 'julianboelen'), value: 'full' }
                                    ],
                                    onChange: (value) => setAttributes({ maxWidth: value }),
                                    help: __('Maximum content width', 'julianboelen')
                                })
                            )
                        )
                    ),

                    createElement(PanelBody, { 
                        title: __('Typography Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Heading Size', 'julianboelen'),
                                    value: headingSize,
                                    options: [
                                        { label: __('Medium', 'julianboelen'), value: 'medium' },
                                        { label: __('Large', 'julianboelen'), value: 'large' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xlarge' }
                                    ],
                                    onChange: (value) => setAttributes({ headingSize: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Text Size', 'julianboelen'),
                                    value: textSize,
                                    options: [
                                        { label: __('Small', 'julianboelen'), value: 'small' },
                                        { label: __('Base', 'julianboelen'), value: 'base' },
                                        { label: __('Large', 'julianboelen'), value: 'large' }
                                    ],
                                    onChange: (value) => setAttributes({ textSize: value })
                                })
                            )
                        )
                    ),
                    
                    createElement(PanelBody, { 
                        title: __('Color Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(BaseControl, {
                                    label: __('Background Color', 'julianboelen'),
                                    help: __('Section background color', 'julianboelen')
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
                                    label: __('Heading Color', 'julianboelen'),
                                    help: __('Color for the main heading', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: headingColor,
                                        onChange: (color) => setAttributes({ headingColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Text Color', 'julianboelen'),
                                    help: __('Color for body text paragraphs', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: textColor,
                                        onChange: (color) => setAttributes({ textColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        )
                    ),

                    createElement(PanelBody, { 
                        title: __('Spacing Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(RangeControl, {
                                    label: __('Padding Top', 'julianboelen'),
                                    value: parseInt(paddingTop),
                                    onChange: (value) => setAttributes({ paddingTop: value.toString() }),
                                    min: 0,
                                    max: 200,
                                    step: 4,
                                    help: __('Top padding in pixels', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(RangeControl, {
                                    label: __('Padding Bottom', 'julianboelen'),
                                    value: parseInt(paddingBottom),
                                    onChange: (value) => setAttributes({ paddingBottom: value.toString() }),
                                    min: 0,
                                    max: 200,
                                    step: 4,
                                    help: __('Bottom padding in pixels', 'julianboelen')
                                })
                            )
                        )
                    )
                ),
                
                // PROFESSIONAL editor preview with realistic styling
                createElement('section', { 
                    ...useBlockProps({
                        className: 'section-title-text-horizontal-preview w-full px-4 sm:px-6 lg:px-8',
                        style: { 
                            backgroundColor: backgroundColor,
                            ...getPaddingStyle(),
                            border: '2px dashed #e5e7eb',
                            borderRadius: '8px'
                        }
                    })
                },
                    createElement('div', { 
                        className: `${maxWidthClass} mx-auto`
                    },
                        createElement('div', { 
                            className: `grid grid-cols-1 lg:grid-cols-12 ${gapClass} ${alignmentClass}`
                        },
                            // Left Column: Heading
                            createElement('div', { 
                                className: `col-span-1 ${columnClasses.left}`
                            },
                                createElement(RichText, {
                                    tagName: 'h1',
                                    className: `${headingSizeClass} font-bold leading-tight`,
                                    style: { color: headingColor },
                                    value: heading,
                                    onChange: (value) => setAttributes({ heading: value }),
                                    placeholder: __('Enter your heading here...', 'julianboelen'),
                                    allowedFormats: ['core/bold', 'core/italic']
                                })
                            ),
                            
                            // Right Column: Body Text
                            createElement('div', { 
                                className: `col-span-1 ${columnClasses.right}`,
                                style: { display: 'flex', flexDirection: 'column', gap: `${paragraphSpacing * 4}px` }
                            },
                                createElement(RichText, {
                                    tagName: 'p',
                                    className: `${textSizeClass} leading-relaxed`,
                                    style: { color: textColor },
                                    value: paragraph1,
                                    onChange: (value) => setAttributes({ paragraph1: value }),
                                    placeholder: __('Enter first paragraph...', 'julianboelen'),
                                    allowedFormats: ['core/bold', 'core/italic', 'core/link']
                                }),
                                
                                showParagraph2 && createElement(RichText, {
                                    tagName: 'p',
                                    className: `${textSizeClass} leading-relaxed`,
                                    style: { color: textColor },
                                    value: paragraph2,
                                    onChange: (value) => setAttributes({ paragraph2: value }),
                                    placeholder: __('Enter second paragraph...', 'julianboelen'),
                                    allowedFormats: ['core/bold', 'core/italic', 'core/link']
                                })
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