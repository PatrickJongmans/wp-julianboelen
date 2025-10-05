(function() {
    const { registerBlockType } = wp.blocks;
    const { RichText, InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } = wp.blockEditor;
    const { Button, PanelBody, PanelRow, ToggleControl, SelectControl, ColorPicker, TextControl, BaseControl, TextareaControl, RangeControl, Tooltip, Icon, Notice } = wp.components;
    const { Fragment, createElement, useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { useSelect } = wp.data;

    registerBlockType('julianboelen/section-process', {
        apiVersion: 2,
        title: __('Section Process', 'julianboelen'),
        icon: 'list-view',
        category: 'julianboelen-blocks',
        description: __('A streamlined process section with customizable step cards from Process Step custom post type', 'julianboelen'),
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
            sectionTitle: {
                type: 'string',
                default: 'Een gestroomlijnd proces'
            },
            backgroundColor: {
                type: 'string',
                default: '#f9fafb'
            },
            titleColor: {
                type: 'string',
                default: '#111827'
            },
            useCustomPostType: {
                type: 'boolean',
                default: true
            },
            postsPerPage: {
                type: 'number',
                default: -1
            },
            processSteps: {
                type: 'array',
                default: []
            },
            columnsDesktop: {
                type: 'string',
                default: '4'
            },
            columnsTablet: {
                type: 'string',
                default: '2'
            },
            columnsMobile: {
                type: 'string',
                default: '1'
            },
            cardBackgroundColor: {
                type: 'string',
                default: '#ffffff'
            },
            cardTextColor: {
                type: 'string',
                default: '#111827'
            },
            cardDescriptionColor: {
                type: 'string',
                default: '#6b7280'
            },
            enableHoverEffect: {
                type: 'boolean',
                default: true
            },
            cardBorderRadius: {
                type: 'string',
                default: '16'
            },
            gapSize: {
                type: 'string',
                default: '24'
            },
            showStepNumbers: {
                type: 'boolean',
                default: true
            },
            stepNumberStyle: {
                type: 'string',
                default: 'prefix'
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                sectionTitle,
                backgroundColor,
                titleColor,
                useCustomPostType,
                postsPerPage,
                processSteps,
                columnsDesktop,
                columnsTablet,
                columnsMobile,
                cardBackgroundColor,
                cardTextColor,
                cardDescriptionColor,
                enableHoverEffect,
                cardBorderRadius,
                gapSize,
                showStepNumbers,
                stepNumberStyle
            } = attributes;

            const [activeStepIndex, setActiveStepIndex] = useState(null);
            const [expandedPanels, setExpandedPanels] = useState({
                dataSource: true,
                content: false,
                design: false,
                layout: false,
                cards: false
            });

            // Fetch Process Steps from custom post type
            const processStepPosts = useSelect((select) => {
                if (!useCustomPostType) return [];
                
                return select('core').getEntityRecords('postType', 'process_step', {
                    per_page: postsPerPage === -1 ? 100 : postsPerPage,
                    status: 'publish',
                    orderby: 'meta_value_num',
                    meta_key: 'order',
                    order: 'asc'
                });
            }, [useCustomPostType, postsPerPage]);

            // Convert fetched posts to process steps format
            const dynamicProcessSteps = processStepPosts ? processStepPosts.map((post, index) => {
                const featuredMedia = post.featured_media ? 
                    useSelect((select) => select('core').getMedia(post.featured_media)) : null;
                
                return {
                    id: 'step-' + post.id,
                    stepNumber: String(index + 1).padStart(2, '0'),
                    title: post.title.rendered,
                    description: post.content.rendered.replace(/<[^>]*>/g, ''),
                    imageUrl: featuredMedia?.source_url || '',
                    imageAlt: featuredMedia?.alt_text || post.title.rendered,
                    imageId: post.featured_media || null
                };
            }) : [];

            // Determine which steps to display
            const displaySteps = useCustomPostType ? dynamicProcessSteps : processSteps;

            // SOPHISTICATED helper functions
            const getGridColumnsClass = () => {
                const desktopClass = `lg:grid-cols-${columnsDesktop}`;
                const tabletClass = `md:grid-cols-${columnsTablet}`;
                const mobileClass = `grid-cols-${columnsMobile}`;
                return `${mobileClass} ${tabletClass} ${desktopClass}`;
            };

            const getBorderRadiusStyle = () => {
                return `${cardBorderRadius}px`;
            };

            const getGapClass = () => {
                const gapMap = {
                    '16': 'gap-4',
                    '24': 'gap-6',
                    '32': 'gap-8',
                    '40': 'gap-10'
                };
                return gapMap[gapSize] || 'gap-6';
            };

            const updateStep = (index, field, value) => {
                const updatedSteps = [...processSteps];
                updatedSteps[index] = {
                    ...updatedSteps[index],
                    [field]: value
                };
                setAttributes({ processSteps: updatedSteps });
            };

            const addStep = () => {
                const newStepNumber = String(processSteps.length + 1).padStart(2, '0');
                const newStep = {
                    id: `step-${Date.now()}`,
                    stepNumber: newStepNumber,
                    title: `Step ${newStepNumber}`,
                    description: 'Enter step description here.',
                    imageUrl: '',
                    imageAlt: '',
                    imageId: null
                };
                setAttributes({ processSteps: [...processSteps, newStep] });
            };

            const removeStep = (index) => {
                if (processSteps.length <= 1) {
                    return;
                }
                const updatedSteps = processSteps.filter((_, i) => i !== index);
                const renumberedSteps = updatedSteps.map((step, i) => ({
                    ...step,
                    stepNumber: String(i + 1).padStart(2, '0')
                }));
                setAttributes({ processSteps: renumberedSteps });
                if (activeStepIndex === index) {
                    setActiveStepIndex(null);
                }
            };

            const moveStep = (index, direction) => {
                const newIndex = direction === 'up' ? index - 1 : index + 1;
                if (newIndex < 0 || newIndex >= processSteps.length) {
                    return;
                }
                const updatedSteps = [...processSteps];
                [updatedSteps[index], updatedSteps[newIndex]] = [updatedSteps[newIndex], updatedSteps[index]];
                const renumberedSteps = updatedSteps.map((step, i) => ({
                    ...step,
                    stepNumber: String(i + 1).padStart(2, '0')
                }));
                setAttributes({ processSteps: renumberedSteps });
            };

            const togglePanel = (panelName) => {
                setExpandedPanels({
                    ...expandedPanels,
                    [panelName]: !expandedPanels[panelName]
                });
            };

            const renderStepNumberDisplay = (stepNumber) => {
                if (!showStepNumbers) return null;
                
                if (stepNumberStyle === 'badge') {
                    return createElement('span', {
                        className: 'inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-bold mb-2'
                    }, stepNumber);
                }
                
                if (stepNumberStyle === 'prefix') {
                    return stepNumber + '. ';
                }
                
                return null;
            };

            // PROFESSIONAL editor preview
            return createElement(Fragment, null,
                // SOPHISTICATED InspectorControls
                createElement(InspectorControls, null,
                    // Data Source Panel
                    createElement(PanelBody, {
                        title: __('Data Source', 'julianboelen'),
                        initialOpen: expandedPanels.dataSource,
                        onToggle: () => togglePanel('dataSource')
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(ToggleControl, {
                                    label: __('Use Process Step Post Type', 'julianboelen'),
                                    checked: useCustomPostType,
                                    onChange: (value) => setAttributes({ useCustomPostType: value }),
                                    help: useCustomPostType 
                                        ? __('Automatically displaying Process Steps from WordPress', 'julianboelen')
                                        : __('Using manual step configuration', 'julianboelen')
                                })
                            )
                        ),
                        useCustomPostType && createElement(Fragment, null,
                            createElement(PanelRow, null,
                                createElement('div', { style: { width: '100%', marginTop: '16px' } },
                                    createElement(RangeControl, {
                                        label: __('Number of Steps to Display', 'julianboelen'),
                                        value: postsPerPage === -1 ? 100 : postsPerPage,
                                        onChange: (value) => setAttributes({ postsPerPage: value === 100 ? -1 : value }),
                                        min: 1,
                                        max: 100,
                                        help: postsPerPage === -1 
                                            ? __('Displaying all Process Steps', 'julianboelen')
                                            : __('Limit the number of Process Steps shown', 'julianboelen')
                                    })
                                )
                            ),
                            createElement(PanelRow, null,
                                createElement(Notice, {
                                    status: 'info',
                                    isDismissible: false
                                },
                                    createElement('p', { style: { margin: 0 } },
                                        dynamicProcessSteps.length > 0
                                            ? __(`Found ${dynamicProcessSteps.length} Process Step(s). Steps are ordered by the 'order' custom field.`, 'julianboelen')
                                            : __('No Process Steps found. Create some Process Steps in WordPress first.', 'julianboelen')
                                    )
                                )
                            )
                        ),
                        !useCustomPostType && createElement(PanelRow, null,
                            createElement(Notice, {
                                status: 'warning',
                                isDismissible: false
                            },
                                createElement('p', { style: { margin: 0 } },
                                    __('Manual mode: Manage steps in the "Process Steps" panel below.', 'julianboelen')
                                )
                            )
                        )
                    ),

                    // Content Settings Panel
                    createElement(PanelBody, {
                        title: __('Content Settings', 'julianboelen'),
                        initialOpen: expandedPanels.content,
                        onToggle: () => togglePanel('content')
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
                            createElement('div', { style: { width: '100%', marginTop: '16px' } },
                                createElement(ToggleControl, {
                                    label: __('Show Step Numbers', 'julianboelen'),
                                    checked: showStepNumbers,
                                    onChange: (value) => setAttributes({ showStepNumbers: value }),
                                    help: __('Display step numbers on cards', 'julianboelen')
                                })
                            )
                        ),
                        showStepNumbers && createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '16px' } },
                                createElement(SelectControl, {
                                    label: __('Step Number Style', 'julianboelen'),
                                    value: stepNumberStyle,
                                    options: [
                                        { label: __('Prefix (01.)', 'julianboelen'), value: 'prefix' },
                                        { label: __('Badge', 'julianboelen'), value: 'badge' },
                                        { label: __('None', 'julianboelen'), value: 'none' }
                                    ],
                                    onChange: (value) => setAttributes({ stepNumberStyle: value })
                                })
                            )
                        )
                    ),

                    // Design Settings Panel
                    createElement(PanelBody, {
                        title: __('Design Settings', 'julianboelen'),
                        initialOpen: expandedPanels.design,
                        onToggle: () => togglePanel('design')
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
                                createElement(SelectControl, {
                                    label: __('Card Border Radius', 'julianboelen'),
                                    value: cardBorderRadius,
                                    options: [
                                        { label: __('None (0px)', 'julianboelen'), value: '0' },
                                        { label: __('Small (8px)', 'julianboelen'), value: '8' },
                                        { label: __('Medium (12px)', 'julianboelen'), value: '12' },
                                        { label: __('Large (16px)', 'julianboelen'), value: '16' },
                                        { label: __('Extra Large (24px)', 'julianboelen'), value: '24' }
                                    ],
                                    onChange: (value) => setAttributes({ cardBorderRadius: value })
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '16px' } },
                                createElement(ToggleControl, {
                                    label: __('Enable Hover Effect', 'julianboelen'),
                                    checked: enableHoverEffect,
                                    onChange: (value) => setAttributes({ enableHoverEffect: value }),
                                    help: __('Add shadow effect on card hover', 'julianboelen')
                                })
                            )
                        )
                    ),

                    // Layout Settings Panel
                    createElement(PanelBody, {
                        title: __('Layout Settings', 'julianboelen'),
                        initialOpen: expandedPanels.layout,
                        onToggle: () => togglePanel('layout')
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
                                createElement(SelectControl, {
                                    label: __('Desktop Columns', 'julianboelen'),
                                    value: columnsDesktop,
                                    options: [
                                        { label: __('2 Columns', 'julianboelen'), value: '2' },
                                        { label: __('3 Columns', 'julianboelen'), value: '3' },
                                        { label: __('4 Columns', 'julianboelen'), value: '4' }
                                    ],
                                    onChange: (value) => setAttributes({ columnsDesktop: value }),
                                    help: __('Number of columns on desktop screens', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '16px' } },
                                createElement(SelectControl, {
                                    label: __('Tablet Columns', 'julianboelen'),
                                    value: columnsTablet,
                                    options: [
                                        { label: __('1 Column', 'julianboelen'), value: '1' },
                                        { label: __('2 Columns', 'julianboelen'), value: '2' },
                                        { label: __('3 Columns', 'julianboelen'), value: '3' }
                                    ],
                                    onChange: (value) => setAttributes({ columnsTablet: value }),
                                    help: __('Number of columns on tablet screens', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '16px' } },
                                createElement(SelectControl, {
                                    label: __('Mobile Columns', 'julianboelen'),
                                    value: columnsMobile,
                                    options: [
                                        { label: __('1 Column', 'julianboelen'), value: '1' },
                                        { label: __('2 Columns', 'julianboelen'), value: '2' }
                                    ],
                                    onChange: (value) => setAttributes({ columnsMobile: value }),
                                    help: __('Number of columns on mobile screens', 'julianboelen')
                                })
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '16px' } },
                                createElement(SelectControl, {
                                    label: __('Gap Size', 'julianboelen'),
                                    value: gapSize,
                                    options: [
                                        { label: __('Small (16px)', 'julianboelen'), value: '16' },
                                        { label: __('Medium (24px)', 'julianboelen'), value: '24' },
                                        { label: __('Large (32px)', 'julianboelen'), value: '32' },
                                        { label: __('Extra Large (40px)', 'julianboelen'), value: '40' }
                                    ],
                                    onChange: (value) => setAttributes({ gapSize: value }),
                                    help: __('Space between cards', 'julianboelen')
                                })
                            )
                        )
                    ),

                    // Card Styling Panel
                    createElement(PanelBody, {
                        title: __('Card Styling', 'julianboelen'),
                        initialOpen: expandedPanels.cards,
                        onToggle: () => togglePanel('cards')
                    },
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%' } },
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
                                        color: cardTextColor,
                                        onChange: (color) => setAttributes({ cardTextColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        ),
                        createElement(PanelRow, null,
                            createElement('div', { style: { width: '100%', marginTop: '20px' } },
                                createElement(BaseControl, {
                                    label: __('Card Description Color', 'julianboelen')
                                },
                                    createElement(ColorPicker, {
                                        color: cardDescriptionColor,
                                        onChange: (color) => setAttributes({ cardDescriptionColor: color.hex }),
                                        disableAlpha: false
                                    })
                                )
                            )
                        )
                    ),

                    // Process Steps Management Panel (only shown in manual mode)
                    !useCustomPostType && createElement(PanelBody, {
                        title: __('Manual Process Steps', 'julianboelen'),
                        initialOpen: false
                    },
                        createElement('div', { style: { marginBottom: '16px' } },
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
                                    border: '1px solid #ddd',
                                    borderRadius: '4px',
                                    padding: '12px',
                                    marginBottom: '12px',
                                    backgroundColor: activeStepIndex === index ? '#f0f0f0' : '#fff'
                                }
                            },
                                createElement('div', {
                                    style: {
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        alignItems: 'center',
                                        marginBottom: '8px'
                                    }
                                },
                                    createElement('strong', null, `${__('Step', 'julianboelen')} ${step.stepNumber}`),
                                    createElement('div', { style: { display: 'flex', gap: '4px' } },
                                        index > 0 && createElement(Button, {
                                            isSmall: true,
                                            icon: 'arrow-up-alt2',
                                            onClick: () => moveStep(index, 'up'),
                                            label: __('Move Up', 'julianboelen')
                                        }),
                                        index < processSteps.length - 1 && createElement(Button, {
                                            isSmall: true,
                                            icon: 'arrow-down-alt2',
                                            onClick: () => moveStep(index, 'down'),
                                            label: __('Move Down', 'julianboelen')
                                        }),
                                        createElement(Button, {
                                            isSmall: true,
                                            icon: activeStepIndex === index ? 'arrow-up' : 'arrow-down',
                                            onClick: () => setActiveStepIndex(activeStepIndex === index ? null : index),
                                            label: activeStepIndex === index ? __('Collapse', 'julianboelen') : __('Expand', 'julianboelen')
                                        }),
                                        processSteps.length > 1 && createElement(Button, {
                                            isSmall: true,
                                            isDestructive: true,
                                            icon: 'trash',
                                            onClick: () => removeStep(index),
                                            label: __('Remove Step', 'julianboelen')
                                        })
                                    )
                                ),
                                activeStepIndex === index && createElement('div', { style: { marginTop: '12px' } },
                                    createElement(TextControl, {
                                        label: __('Step Title', 'julianboelen'),
                                        value: step.title,
                                        onChange: (value) => updateStep(index, 'title', value)
                                    }),
                                    createElement(TextareaControl, {
                                        label: __('Step Description', 'julianboelen'),
                                        value: step.description,
                                        onChange: (value) => updateStep(index, 'description', value),
                                        rows: 3,
                                        style: { marginTop: '12px' }
                                    }),
                                    createElement('div', { style: { marginTop: '12px' } },
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
                                                                marginBottom: '8px',
                                                                borderRadius: '4px'
                                                            }
                                                        }),
                                                        createElement('div', { style: { display: 'flex', gap: '8px' } },
                                                            createElement(Button, {
                                                                variant: step.imageUrl ? 'secondary' : 'primary',
                                                                onClick: open
                                                            }, step.imageUrl ? __('Replace Image', 'julianboelen') : __('Select Image', 'julianboelen')),
                                                            step.imageUrl && createElement(Button, {
                                                                variant: 'tertiary',
                                                                isDestructive: true,
                                                                onClick: () => {
                                                                    updateStep(index, 'imageUrl', '');
                                                                    updateStep(index, 'imageId', null);
                                                                }
                                                            }, __('Remove', 'julianboelen'))
                                                        )
                                                    )
                                                })
                                            )
                                        )
                                    ),
                                    createElement(TextControl, {
                                        label: __('Image Alt Text', 'julianboelen'),
                                        value: step.imageAlt,
                                        onChange: (value) => updateStep(index, 'imageAlt', value),
                                        help: __('Describe the image for accessibility', 'julianboelen'),
                                        style: { marginTop: '12px' }
                                    })
                                )
                            )
                        )
                    )
                ),

                // PROFESSIONAL editor preview
                createElement('section', {
                    ...useBlockProps({
                        className: 'section-process-block-editor',
                        style: {
                            backgroundColor: backgroundColor,
                            padding: '48px 16px',
                            minHeight: '400px'
                        }
                    })
                },
                    createElement('div', { className: 'max-w-7xl mx-auto' },
                        // Section Title
                        createElement(RichText, {
                            tagName: 'h2',
                            className: 'section-process-title',
                            style: {
                                color: titleColor,
                                fontSize: 'clamp(2rem, 5vw, 3rem)',
                                fontWeight: 'bold',
                                marginBottom: '48px',
                                lineHeight: '1.2'
                            },
                            value: sectionTitle,
                            onChange: (value) => setAttributes({ sectionTitle: value }),
                            placeholder: __('Enter section title...', 'julianboelen')
                        }),

                        // Data source indicator
                        useCustomPostType && createElement('div', {
                            style: {
                                marginBottom: '24px',
                                padding: '12px 16px',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderRadius: '8px',
                                border: '1px solid #3b82f6',
                                textAlign: 'center'
                            }
                        },
                            createElement('p', {
                                style: {
                                    margin: 0,
                                    color: '#1e40af',
                                    fontSize: '0.875rem',
                                    fontWeight: '500'
                                }
                            }, displaySteps.length > 0 
                                ? __(`📊 Displaying ${displaySteps.length} Process Step(s) from WordPress`, 'julianboelen')
                                : __('⚠️ No Process Steps found. Create some in WordPress!', 'julianboelen')
                            )
                        ),

                        // Process Cards Grid
                        displaySteps.length > 0 ? createElement('div', {
                            className: `grid ${getGridColumnsClass()} ${getGapClass()}`,
                            style: {
                                width: '100%'
                            }
                        },
                            displaySteps.map((step, index) => 
                                createElement('div', {
                                    key: step.id,
                                    className: 'process-card',
                                    style: {
                                        backgroundColor: cardBackgroundColor,
                                        borderRadius: getBorderRadiusStyle(),
                                        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                                        overflow: 'hidden',
                                        transition: 'all 0.3s ease',
                                        cursor: useCustomPostType ? 'default' : 'pointer',
                                        border: !useCustomPostType && activeStepIndex === index ? '2px solid #2563eb' : '2px solid transparent'
                                    },
                                    onClick: () => !useCustomPostType && setActiveStepIndex(activeStepIndex === index ? null : index)
                                },
                                    // Image Container
                                    step.imageUrl && createElement('div', {
                                        style: {
                                            aspectRatio: '4/3',
                                            overflow: 'hidden',
                                            position: 'relative'
                                        }
                                    },
                                        createElement('img', {
                                            src: step.imageUrl,
                                            alt: step.imageAlt || step.title,
                                            style: {
                                                width: '100%',
                                                height: '100%',
                                                objectFit: 'cover'
                                            }
                                        })
                                    ),

                                    // Card Content
                                    createElement('div', {
                                        style: {
                                            padding: '24px'
                                        }
                                    },
                                        createElement('h3', {
                                            style: {
                                                color: cardTextColor,
                                                fontSize: '1.25rem',
                                                fontWeight: 'bold',
                                                marginBottom: '12px',
                                                lineHeight: '1.4'
                                            }
                                        },
                                            stepNumberStyle === 'badge' && renderStepNumberDisplay(step.stepNumber),
                                            stepNumberStyle === 'prefix' && renderStepNumberDisplay(step.stepNumber),
                                            step.title
                                        ),
                                        createElement('div', {
                                            style: {
                                                color: cardDescriptionColor,
                                                fontSize: '0.875rem',
                                                lineHeight: '1.6',
                                                margin: 0
                                            },
                                            dangerouslySetInnerHTML: { __html: step.description }
                                        })
                                    )
                                )
                            )
                        ) : createElement('div', {
                            style: {
                                padding: '48px 24px',
                                textAlign: 'center',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderRadius: '8px',
                                border: '2px dashed #ef4444'
                            }
                        },
                            createElement('p', {
                                style: {
                                    margin: 0,
                                    color: '#991b1b',
                                    fontSize: '1rem',
                                    fontWeight: '500'
                                }
                            }, useCustomPostType 
                                ? __('No Process Steps found. Please create some Process Steps in WordPress first.', 'julianboelen')
                                : __('No manual steps configured. Add steps using the sidebar panel.', 'julianboelen')
                            )
                        ),

                        // Helper text
                        createElement('div', {
                            style: {
                                marginTop: '24px',
                                padding: '16px',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderRadius: '8px',
                                border: '1px dashed #3b82f6',
                                textAlign: 'center'
                            }
                        },
                            createElement('p', {
                                style: {
                                    margin: 0,
                                    color: '#1e40af',
                                    fontSize: '0.875rem'
                                }
                            }, useCustomPostType 
                                ? __('💡 Steps are automatically fetched from the Process Step post type and ordered by the "order" custom field', 'julianboelen')
                                : __('💡 Click on a card to edit it, or use the sidebar to manage all steps', 'julianboelen')
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