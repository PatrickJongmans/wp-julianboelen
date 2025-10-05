(function() {
    const { registerBlockType } = wp.blocks;
    const { RichText, InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } = wp.blockEditor;
    const { Button, PanelBody, PanelRow, ToggleControl, RangeControl, SelectControl, ColorPicker, TextControl, TextareaControl, BaseControl, IconButton, Toolbar, ToolbarButton, RadioControl, Notice, Spinner } = wp.components;
    const { Fragment, createElement, useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { useSelect } = wp.data;

    registerBlockType('julianboelen/section-process', {
        apiVersion: 2,
        title: __('Section Process', 'julianboelen'),
        icon: 'list-view',
        category: 'julianboelen-blocks',
        description: __('A streamlined process section with customizable step cards', 'julianboelen'),
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
            sectionTitle: { type: 'string', default: 'Een gestroomlijnd proces' },
            dataSource: { type: 'string', default: 'custom' },
            postsToShow: { type: 'number', default: 4 },
            orderBy: { type: 'string', default: 'menu_order' },
            order: { type: 'string', default: 'ASC' },
            backgroundColor: { type: 'string', default: '#f9fafb' },
            titleColor: { type: 'string', default: '#111827' },
            cardBackgroundColor: { type: 'string', default: '#ffffff' },
            cardTextColor: { type: 'string', default: '#374151' },
            cardTitleColor: { type: 'string', default: '#111827' },
            enableHoverEffect: { type: 'boolean', default: true },
            columnsDesktop: { type: 'number', default: 4 },
            columnsTablet: { type: 'number', default: 2 },
            columnsMobile: { type: 'number', default: 1 },
            cardGap: { type: 'string', default: '6' },
            processSteps: {
                type: 'array',
                default: [
                    {
                        id: 'step-1',
                        stepNumber: '01',
                        title: 'Solliciteren',
                        description: 'Solliciteer door jouw CV of Linkedin profiel op te sturen of direct op een vacature te reageren.',
                        imageUrl: 'https://images.unsplash.com/photo-1595147389795-37094173bfd8?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHwzfHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1080&w=800&h=600&fit=crop',
                        imageAlt: 'Solliciteren',
                        imageId: null
                    },
                    {
                        id: 'step-2',
                        stepNumber: '02',
                        title: 'Profiel opstellen',
                        description: 'Solliciteer door jouw CV of Linkedin profiel op te sturen of direct op een vacature te reageren.',
                        imageUrl: 'https://images.unsplash.com/photo-1595147389795-37094173bfd8?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHwzfHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1080&w=800&h=600&fit=crop',
                        imageAlt: 'Profiel opstellen',
                        imageId: null
                    },
                    {
                        id: 'step-3',
                        stepNumber: '03',
                        title: 'Op gesprek',
                        description: 'Solliciteer door jouw CV of Linkedin profiel op te sturen of direct op een vacature te reageren.',
                        imageUrl: 'https://images.unsplash.com/photo-1488372759477-a7f4aa078cb6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHw1fHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1080&w=800&h=600&fit=crop',
                        imageAlt: 'Op gesprek',
                        imageId: null
                    },
                    {
                        id: 'step-4',
                        stepNumber: '04',
                        title: 'Aan de slag!',
                        description: 'Solliciteer door jouw CV of Linkedin profiel op te sturen of direct op een vacature te reageren.',
                        imageUrl: 'https://images.unsplash.com/photo-1595147389795-37094173bfd8?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHwzfHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1080&w=800&h=600&fit=crop',
                        imageAlt: 'Aan de slag',
                        imageId: null
                    }
                ]
            },
            paddingTop: { type: 'string', default: '12' },
            paddingBottom: { type: 'string', default: '12' },
            titleMarginBottom: { type: 'string', default: '8' },
            cardBorderRadius: { type: 'string', default: 'lg' },
            imageBorderRadius: { type: 'string', default: 'none' },
            shadowStyle: { type: 'string', default: 'md' },
            hoverShadowStyle: { type: 'string', default: 'lg' }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                sectionTitle,
                dataSource,
                postsToShow,
                orderBy,
                order,
                backgroundColor,
                titleColor,
                cardBackgroundColor,
                cardTextColor,
                cardTitleColor,
                enableHoverEffect,
                columnsDesktop,
                columnsTablet,
                columnsMobile,
                cardGap,
                processSteps,
                paddingTop,
                paddingBottom,
                titleMarginBottom,
                cardBorderRadius,
                imageBorderRadius,
                shadowStyle,
                hoverShadowStyle
            } = attributes;

            const [selectedStepIndex, setSelectedStepIndex] = useState(null);
            const [expandedStep, setExpandedStep] = useState(null);

            // Fetch Process Step posts from WordPress
            const processStepPosts = useSelect((select) => {
                if (dataSource !== 'posts') return [];
                
                return select('core').getEntityRecords('postType', 'process_step', {
                    per_page: postsToShow,
                    orderby: orderBy,
                    order: order,
                    _embed: true
                });
            }, [dataSource, postsToShow, orderBy, order]);

            // Convert posts to steps format
            const getStepsToDisplay = () => {
                if (dataSource === 'posts' && processStepPosts) {
                    return processStepPosts.map((post, index) => {
                        const featuredImage = post._embedded?.['wp:featuredmedia']?.[0];
                        return {
                            id: `post-${post.id}`,
                            stepNumber: String(index + 1).padStart(2, '0'),
                            title: post.title.rendered,
                            description: post.excerpt.rendered.replace(/<[^>]*>/g, ''),
                            imageUrl: featuredImage?.source_url || '',
                            imageAlt: featuredImage?.alt_text || post.title.rendered,
                            imageId: featuredImage?.id || null
                        };
                    });
                }
                return processSteps;
            };

            const stepsToDisplay = getStepsToDisplay();

            // Helper functions for dynamic styling
            const getGridColumnsClass = () => {
                const mobileClass = `grid-cols-${columnsMobile}`;
                const tabletClass = `sm:grid-cols-${columnsTablet}`;
                const desktopClass = `lg:grid-cols-${columnsDesktop}`;
                return `${mobileClass} ${tabletClass} ${desktopClass}`;
            };

            const getGapClass = () => {
                return `gap-${cardGap}`;
            };

            const getPaddingClass = () => {
                return `py-${paddingTop} pb-${paddingBottom}`;
            };

            const getTitleMarginClass = () => {
                return `mb-${titleMarginBottom}`;
            };

            const getBorderRadiusClass = (type) => {
                if (type === 'none') return '';
                return `rounded-${type}`;
            };

            const getShadowClass = (shadow) => {
                if (shadow === 'none') return '';
                return `shadow-${shadow}`;
            };

            const getHoverShadowClass = () => {
                if (!enableHoverEffect || hoverShadowStyle === 'none') return '';
                return `hover:shadow-${hoverShadowStyle}`;
            };

            // Step management functions (only for custom mode)
            const updateStep = (index, field, value) => {
                const newSteps = [...processSteps];
                newSteps[index] = {
                    ...newSteps[index],
                    [field]: value
                };
                setAttributes({ processSteps: newSteps });
            };

            const addStep = () => {
                const newStepNumber = String(processSteps.length + 1).padStart(2, '0');
                const newStep = {
                    id: `step-${Date.now()}`,
                    stepNumber: newStepNumber,
                    title: `Step ${newStepNumber}`,
                    description: 'Enter step description here.',
                    imageUrl: 'https://images.unsplash.com/photo-1595147389795-37094173bfd8?w=800&h=600&fit=crop',
                    imageAlt: `Step ${newStepNumber}`,
                    imageId: null
                };
                setAttributes({ processSteps: [...processSteps, newStep] });
            };

            const removeStep = (index) => {
                if (processSteps.length <= 1) {
                    alert(__('You must have at least one step.', 'julianboelen'));
                    return;
                }
                const newSteps = processSteps.filter((_, i) => i !== index);
                const renumberedSteps = newSteps.map((step, i) => ({
                    ...step,
                    stepNumber: String(i + 1).padStart(2, '0')
                }));
                setAttributes({ processSteps: renumberedSteps });
                if (selectedStepIndex === index) {
                    setSelectedStepIndex(null);
                }
            };

            const moveStep = (index, direction) => {
                const newIndex = direction === 'up' ? index - 1 : index + 1;
                if (newIndex < 0 || newIndex >= processSteps.length) return;
                
                const newSteps = [...processSteps];
                [newSteps[index], newSteps[newIndex]] = [newSteps[newIndex], newSteps[index]];
                
                const renumberedSteps = newSteps.map((step, i) => ({
                    ...step,
                    stepNumber: String(i + 1).padStart(2, '0')
                }));
                
                setAttributes({ processSteps: renumberedSteps });
                setSelectedStepIndex(newIndex);
            };

            const duplicateStep = (index) => {
                const stepToDuplicate = processSteps[index];
                const newStep = {
                    ...stepToDuplicate,
                    id: `step-${Date.now()}`,
                    stepNumber: String(processSteps.length + 1).padStart(2, '0')
                };
                setAttributes({ processSteps: [...processSteps, newStep] });
            };

            return createElement(Fragment, null,
                createElement(InspectorControls, null,
                    // Data Source Panel
                    createElement(PanelBody, { 
                        title: __('Data Source', 'julianboelen'), 
                        initialOpen: true 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(RadioControl, {
                                    label: __('Content Source', 'julianboelen'),
                                    help: __('Choose between custom steps or Process Step posts', 'julianboelen'),
                                    selected: dataSource,
                                    options: [
                                        { label: __('Custom Steps', 'julianboelen'), value: 'custom' },
                                        { label: __('Process Step Posts', 'julianboelen'), value: 'posts' }
                                    ],
                                    onChange: (value) => setAttributes({ dataSource: value })
                                })
                            )
                        ),
                        dataSource === 'posts' && createElement(Fragment, null,
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%', marginTop: '15px' } },
                                    createElement(RangeControl, {
                                        label: __('Number of Posts', 'julianboelen'),
                                        value: postsToShow,
                                        onChange: (value) => setAttributes({ postsToShow: value }),
                                        min: 1,
                                        max: 12,
                                        step: 1,
                                        help: __('How many process steps to display', 'julianboelen')
                                    })
                                )
                            ),
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(SelectControl, {
                                        label: __('Order By', 'julianboelen'),
                                        value: orderBy,
                                        options: [
                                            { label: __('Custom Order (Menu Order)', 'julianboelen'), value: 'menu_order' },
                                            { label: __('Date Published', 'julianboelen'), value: 'date' },
                                            { label: __('Title', 'julianboelen'), value: 'title' }
                                        ],
                                        onChange: (value) => setAttributes({ orderBy: value })
                                    })
                                )
                            ),
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%' } },
                                    createElement(SelectControl, {
                                        label: __('Order', 'julianboelen'),
                                        value: order,
                                        options: [
                                            { label: __('Ascending', 'julianboelen'), value: 'ASC' },
                                            { label: __('Descending', 'julianboelen'), value: 'DESC' }
                                        ],
                                        onChange: (value) => setAttributes({ order: value })
                                    })
                                )
                            ),
                            processStepPosts === null && createElement(PanelRow, null,
                                createElement('div', { style: { padding: '20px', textAlign: 'center' } },
                                    createElement(Spinner)
                                )
                            ),
                            processStepPosts && processStepPosts.length === 0 && createElement(PanelRow, null,
                                createElement(Notice, {
                                    status: 'warning',
                                    isDismissible: false
                                }, __('No Process Step posts found. Create some posts first.', 'julianboelen'))
                            )
                        )
                    ),
                    
                    createElement(PanelBody, { 
                        title: __('Section Settings', 'julianboelen'), 
                        initialOpen: true 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(TextControl, {
                                    label: __('Section Title', 'julianboelen'),
                                    value: sectionTitle,
                                    onChange: (value) => setAttributes({ sectionTitle: value }),
                                    help: __('Main heading for the process section', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '15px' } },
                                createElement(RangeControl, {
                                    label: __('Top Padding', 'julianboelen'),
                                    value: parseInt(paddingTop),
                                    onChange: (value) => setAttributes({ paddingTop: String(value) }),
                                    min: 0,
                                    max: 32,
                                    step: 1
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(RangeControl, {
                                    label: __('Bottom Padding', 'julianboelen'),
                                    value: parseInt(paddingBottom),
                                    onChange: (value) => setAttributes({ paddingBottom: String(value) }),
                                    min: 0,
                                    max: 32,
                                    step: 1
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(RangeControl, {
                                    label: __('Title Bottom Margin', 'julianboelen'),
                                    value: parseInt(titleMarginBottom),
                                    onChange: (value) => setAttributes({ titleMarginBottom: String(value) }),
                                    min: 0,
                                    max: 24,
                                    step: 1
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
                                    label: __('Title Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: titleColor,
                                        onChange: (color) => setAttributes({ titleColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Card Background Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: cardBackgroundColor,
                                        onChange: (color) => setAttributes({ cardBackgroundColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Card Title Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: cardTitleColor,
                                        onChange: (color) => setAttributes({ cardTitleColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Card Text Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: cardTextColor,
                                        onChange: (color) => setAttributes({ cardTextColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        )
                    ),
                    
                    createElement(PanelBody, { 
                        title: __('Layout Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(RangeControl, {
                                    label: __('Columns (Desktop)', 'julianboelen'),
                                    value: columnsDesktop,
                                    onChange: (value) => setAttributes({ columnsDesktop: value }),
                                    min: 1,
                                    max: 6,
                                    step: 1
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(RangeControl, {
                                    label: __('Columns (Tablet)', 'julianboelen'),
                                    value: columnsTablet,
                                    onChange: (value) => setAttributes({ columnsTablet: value }),
                                    min: 1,
                                    max: 4,
                                    step: 1
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(RangeControl, {
                                    label: __('Columns (Mobile)', 'julianboelen'),
                                    value: columnsMobile,
                                    onChange: (value) => setAttributes({ columnsMobile: value }),
                                    min: 1,
                                    max: 2,
                                    step: 1
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Card Gap', 'julianboelen'),
                                    value: cardGap,
                                    options: [
                                        { label: __('Extra Small (0.5rem)', 'julianboelen'), value: '2' },
                                        { label: __('Small (1rem)', 'julianboelen'), value: '4' },
                                        { label: __('Medium (1.5rem)', 'julianboelen'), value: '6' },
                                        { label: __('Large (2rem)', 'julianboelen'), value: '8' },
                                        { label: __('Extra Large (3rem)', 'julianboelen'), value: '12' }
                                    ],
                                    onChange: (value) => setAttributes({ cardGap: value })
                                })
                            )
                        )
                    ),
                    
                    createElement(PanelBody, { 
                        title: __('Card Style Settings', 'julianboelen'), 
                        initialOpen: false 
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Card Border Radius', 'julianboelen'),
                                    value: cardBorderRadius,
                                    options: [
                                        { label: __('None', 'julianboelen'), value: 'none' },
                                        { label: __('Small', 'julianboelen'), value: 'sm' },
                                        { label: __('Medium', 'julianboelen'), value: 'md' },
                                        { label: __('Large', 'julianboelen'), value: 'lg' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xl' },
                                        { label: __('2XL', 'julianboelen'), value: '2xl' },
                                        { label: __('Full', 'julianboelen'), value: 'full' }
                                    ],
                                    onChange: (value) => setAttributes({ cardBorderRadius: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Image Border Radius', 'julianboelen'),
                                    value: imageBorderRadius,
                                    options: [
                                        { label: __('None', 'julianboelen'), value: 'none' },
                                        { label: __('Small', 'julianboelen'), value: 'sm' },
                                        { label: __('Medium', 'julianboelen'), value: 'md' },
                                        { label: __('Large', 'julianboelen'), value: 'lg' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xl' }
                                    ],
                                    onChange: (value) => setAttributes({ imageBorderRadius: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Shadow Style', 'julianboelen'),
                                    value: shadowStyle,
                                    options: [
                                        { label: __('None', 'julianboelen'), value: 'none' },
                                        { label: __('Small', 'julianboelen'), value: 'sm' },
                                        { label: __('Medium', 'julianboelen'), value: 'md' },
                                        { label: __('Large', 'julianboelen'), value: 'lg' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xl' },
                                        { label: __('2XL', 'julianboelen'), value: '2xl' }
                                    ],
                                    onChange: (value) => setAttributes({ shadowStyle: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement(ToggleControl, {
                                label: __('Enable Hover Effect', 'julianboelen'),
                                checked: enableHoverEffect,
                                onChange: (value) => setAttributes({ enableHoverEffect: value }),
                                help: __('Add shadow effect on card hover', 'julianboelen')
                            })
                        ),
                        enableHoverEffect && createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Hover Shadow Style', 'julianboelen'),
                                    value: hoverShadowStyle,
                                    options: [
                                        { label: __('None', 'julianboelen'), value: 'none' },
                                        { label: __('Small', 'julianboelen'), value: 'sm' },
                                        { label: __('Medium', 'julianboelen'), value: 'md' },
                                        { label: __('Large', 'julianboelen'), value: 'lg' },
                                        { label: __('Extra Large', 'julianboelen'), value: 'xl' },
                                        { label: __('2XL', 'julianboelen'), value: '2xl' }
                                    ],
                                    onChange: (value) => setAttributes({ hoverShadowStyle: value })
                                })
                            )
                        )
                    ),
                    
                    // Custom Steps Panel - only show in custom mode
                    dataSource === 'custom' && createElement(PanelBody, { 
                        title: __('Custom Process Steps', 'julianboelen'), 
                        initialOpen: true 
                    },
                        createElement('div', { style: { marginBottom: '15px' } },
                            createElement(Button, {
                                variant: 'primary',
                                onClick: addStep,
                                style: { width: '100%' }
                            }, __('+ Add New Step', 'julianboelen'))
                        ),
                        processSteps.map((step, index) => 
                            createElement('div', {
                                key: step.id,
                                style: {
                                    marginBottom: '15px',
                                    padding: '15px',
                                    border: selectedStepIndex === index ? '2px solid #2271b1' : '1px solid #ddd',
                                    borderRadius: '4px',
                                    backgroundColor: selectedStepIndex === index ? '#f0f6fc' : '#fff'
                                }
                            },
                                createElement('div', {
                                    style: {
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        alignItems: 'center',
                                        marginBottom: '10px',
                                        cursor: 'pointer'
                                    },
                                    onClick: () => setExpandedStep(expandedStep === index ? null : index)
                                },
                                    createElement('strong', null, `${step.stepNumber}. ${step.title || __('Untitled Step', 'julianboelen')}`),
                                    createElement('span', {
                                        style: { fontSize: '18px' }
                                    }, expandedStep === index ? '▼' : '▶')
                                ),
                                expandedStep === index && createElement(Fragment, null,
                                    createElement('div', { style: { marginBottom: '10px' } },
                                        createElement(TextControl, {
                                            label: __('Step Number', 'julianboelen'),
                                            value: step.stepNumber,
                                            onChange: (value) => updateStep(index, 'stepNumber', value)
                                        })
                                    ),
                                    createElement('div', { style: { marginBottom: '10px' } },
                                        createElement(TextControl, {
                                            label: __('Step Title', 'julianboelen'),
                                            value: step.title,
                                            onChange: (value) => updateStep(index, 'title', value)
                                        })
                                    ),
                                    createElement('div', { style: { marginBottom: '10px' } },
                                        createElement(TextareaControl, {
                                            label: __('Step Description', 'julianboelen'),
                                            value: step.description,
                                            onChange: (value) => updateStep(index, 'description', value),
                                            rows: 3
                                        })
                                    ),
                                    createElement('div', { style: { marginBottom: '10px' } },
                                        createElement(BaseControl, {
                                            label: __('Step Image', 'julianboelen')
                                        },
                                            createElement(MediaUploadCheck, null,
                                                createElement(MediaUpload, {
                                                    onSelect: (media) => {
                                                        updateStep(index, 'imageUrl', media.url);
                                                        updateStep(index, 'imageId', media.id);
                                                        updateStep(index, 'imageAlt', media.alt || step.title);
                                                    },
                                                    allowedTypes: ['image'],
                                                    value: step.imageId,
                                                    render: ({ open }) => createElement('div', null,
                                                        step.imageUrl && createElement('img', {
                                                            src: step.imageUrl,
                                                            alt: step.imageAlt,
                                                            style: {
                                                                width: '100%',
                                                                height: 'auto',
                                                                marginBottom: '10px',
                                                                borderRadius: '4px'
                                                            }
                                                        }),
                                                        createElement('div', {
                                                            style: {
                                                                display: 'flex',
                                                                gap: '10px'
                                                            }
                                                        },
                                                            createElement(Button, {
                                                                onClick: open,
                                                                variant: step.imageUrl ? 'secondary' : 'primary'
                                                            }, step.imageUrl ? __('Replace Image', 'julianboelen') : __('Select Image', 'julianboelen')),
                                                            step.imageUrl && createElement(Button, {
                                                                onClick: () => {
                                                                    updateStep(index, 'imageUrl', '');
                                                                    updateStep(index, 'imageId', null);
                                                                },
                                                                variant: 'secondary',
                                                                isDestructive: true
                                                            }, __('Remove', 'julianboelen'))
                                                        )
                                                    )
                                                })
                                            )
                                        )
                                    ),
                                    createElement('div', { style: { marginBottom: '10px' } },
                                        createElement(TextControl, {
                                            label: __('Image Alt Text', 'julianboelen'),
                                            value: step.imageAlt,
                                            onChange: (value) => updateStep(index, 'imageAlt', value),
                                            help: __('Describe the image for accessibility', 'julianboelen')
                                        })
                                    ),
                                    createElement('div', {
                                        style: {
                                            display: 'flex',
                                            gap: '8px',
                                            flexWrap: 'wrap',
                                            marginTop: '15px',
                                            paddingTop: '15px',
                                            borderTop: '1px solid #ddd'
                                        }
                                    },
                                        createElement(Button, {
                                            variant: 'secondary',
                                            onClick: () => moveStep(index, 'up'),
                                            disabled: index === 0,
                                            size: 'small'
                                        }, __('↑ Move Up', 'julianboelen')),
                                        createElement(Button, {
                                            variant: 'secondary',
                                            onClick: () => moveStep(index, 'down'),
                                            disabled: index === processSteps.length - 1,
                                            size: 'small'
                                        }, __('↓ Move Down', 'julianboelen')),
                                        createElement(Button, {
                                            variant: 'secondary',
                                            onClick: () => duplicateStep(index),
                                            size: 'small'
                                        }, __('Duplicate', 'julianboelen')),
                                        createElement(Button, {
                                            variant: 'secondary',
                                            onClick: () => removeStep(index),
                                            isDestructive: true,
                                            size: 'small'
                                        }, __('Remove', 'julianboelen'))
                                    )
                                )
                            )
                        )
                    )
                ),
                
                // Editor preview
                createElement('section', { 
                    ...useBlockProps({
                        className: `section-process-block w-full px-4 sm:px-6 lg:px-8 ${getPaddingClass()}`,
                        style: { 
                            backgroundColor: backgroundColor,
                            border: '2px dashed #ccc'
                        }
                    })
                },
                    createElement('div', { className: 'max-w-7xl mx-auto' },
                        createElement(RichText, {
                            tagName: 'h2',
                            className: `text-3xl sm:text-4xl lg:text-5xl font-bold ${getTitleMarginClass()}`,
                            style: { color: titleColor },
                            value: sectionTitle,
                            onChange: (value) => setAttributes({ sectionTitle: value }),
                            placeholder: __('Enter section title...', 'julianboelen')
                        }),
                        
                        dataSource === 'posts' && processStepPosts === null && createElement('div', {
                            style: { textAlign: 'center', padding: '40px' }
                        },
                            createElement(Spinner),
                            createElement('p', { style: { marginTop: '10px' } }, __('Loading process steps...', 'julianboelen'))
                        ),
                        
                        dataSource === 'posts' && processStepPosts && processStepPosts.length === 0 && createElement('div', {
                            style: { textAlign: 'center', padding: '40px' }
                        },
                            createElement(Notice, {
                                status: 'warning',
                                isDismissible: false
                            }, __('No Process Step posts found. Please create some posts or switch to custom mode.', 'julianboelen'))
                        ),
                        
                        stepsToDisplay.length > 0 && createElement('div', {
                            className: `grid ${getGridColumnsClass()} ${getGapClass()}`
                        },
                            stepsToDisplay.map((step, index) => 
                                createElement('div', {
                                    key: step.id,
                                    className: `${getBorderRadiusClass(cardBorderRadius)} ${getShadowClass(shadowStyle)} ${getHoverShadowClass()} overflow-hidden transition-shadow duration-300`,
                                    style: {
                                        backgroundColor: cardBackgroundColor,
                                        cursor: dataSource === 'custom' ? 'pointer' : 'default',
                                        border: selectedStepIndex === index ? '2px solid #2271b1' : 'none'
                                    },
                                    onClick: () => {
                                        if (dataSource === 'custom') {
                                            setSelectedStepIndex(index);
                                            setExpandedStep(index);
                                        }
                                    }
                                },
                                    createElement('div', {
                                        className: `aspect-[4/3] overflow-hidden ${getBorderRadiusClass(imageBorderRadius)}`
                                    },
                                        step.imageUrl && createElement('img', {
                                            src: step.imageUrl,
                                            alt: step.imageAlt,
                                            className: 'w-full h-full object-cover',
                                            loading: 'lazy'
                                        })
                                    ),
                                    createElement('div', { className: 'p-6' },
                                        createElement('h3', {
                                            className: 'text-xl font-bold mb-3',
                                            style: { color: cardTitleColor }
                                        }, `${step.stepNumber}. ${step.title}`),
                                        createElement('p', {
                                            className: 'leading-relaxed',
                                            style: { color: cardTextColor }
                                        }, step.description)
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