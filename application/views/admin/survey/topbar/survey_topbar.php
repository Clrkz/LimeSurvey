<?php
$permissions = [];
$buttonsgroup = [];
$buttons = [];
$topbar = [
    'alignment' => [
        'left' => [
            'buttons' => [],
        ],
        'right' => [
            'buttons' => [],
        ],
    ],
];

// Left Buttons for Survey Summary
// TODO: SurveyBar Activation Buttons
// views/admin/survey/surveybar_activation.php
// Survey Activation
if (!$isActive) {
    $hasUpdatePermission = Permission::model()->hasSurveyPermission($sid, 'surveyactivation', 'update');
    // activate
    if ($canactivate) {
        $buttons['activate_survey'] = [
            'url' => $this->createUrl("admin/survey/sa/activate/surveyid/$sid"),
            'name' => gT('Activate this survey'),
            'id' => 'ls-activate-survey',
            'class' => 'btn-success',
        ];
        array_push($topbar['alignment']['left']['buttons'], $buttons['activate_survey']);

        // cant activate
    } else if ($hasUpdatePermission) {
        $permissions['update'] = ['update' => $hasUpdatePermission];
        // TODO: ToolTip for cant activate survey
    }
} else {
    // activate expired survey
    if ($expired) {
        // TODO: ToolTip for expired survey
    } else if ($notstarted) {
        // TODO: ToolTip for not started survey
    }

    // <!-- Stop survey -->
    if ($canactivate) {
        $buttons['stop_survey'] = [
            'url' => $this->createUrl("admin/survey/sa/deactivate/surveyid/$sid"),
            'class' => 'btn-danger btntooltip',
            'icon' => 'fa fa-stop-circle',
            'name' => gT("Stop this survey"),
        ];
        array_push($topbar['alignment']['left']['buttons'], $buttons['stop_survey']);
    }
}

if ($hasSurveyContentPermission) {
    
    // Preview Survey Button
    $title = ($isActive) ? 'preview_survey' : 'execute_survey';
    $name = ($isActive) ? gT('Preview survey') : gT('Execute survey');

    if (safecount($oSurvey->allLanguages) > 1) {
        $buttons[$title] = [];
        foreach ($oSurvey->allLanguages as $language) {
            $buttons[$title.'_'.$language] = [
                'url' => $this->createAbsoluteUrl(
                    "survey/index", 
                    array(
                        'sid' => $sid,
                        'newtest' => "Y",
                        'lang' => $language
                    )
                ),
                'icon' => 'fa fa-cog',
                'iconclass' => 'fa fa-external-link',
                'name' => $name.' ('.getLanguageNameFromCode($language, false).')',
                'class' => ' external',
                'target' => '_blank'
            ];
        }

        $buttonsurvey_preview_dropdown = [
            'class' => 'btn-group',
            'main_button' => [
                'class' => 'dropdown-toggle',
                'datatoggle' => 'dropdown',
                'ariahaspopup' => true,
                'ariaexpanded' => false,
                'icon' => 'fa fa-cog',
                'name' => $name,
                'iconclass' => 'caret',
            ],
            'dropdown' => [
                'class' => 'dropdown-menu',
                'items' => $buttons,
            ],
        ];
        array_push($topbar['alignment']['left']['buttons'], $buttonsurvey_preview_dropdown);
    } else {
        $buttons[$title] = [
            'url' => $this->createAbsoluteUrl(
                "survey/index", 
                array(
                    'sid' => $sid,
                    'newtest' => "Y",
                )
            ),
            'name' => $name,
            'icon' => 'fa fa-cog',
            'iconclass' => 'fa fa-external-link',
            'class' => ' external',
            'target' => '_blank'
        ];

        array_push($topbar['alignment']['left']['buttons'], $buttons[$title]);
    }

}

// tools
// views/admin/surveybar_tools.php
$buttonsgroup['tools'] = [
    'class' => 'btn-group hidden-xs',
    'main_button' => [
        'id' => 'ls-tools-button',
        'class' => 'dropdown-toggle',
        'datatoggle' => 'dropdown',
        'ariahaspopup' => 'true',
        'ariaexpanded' => 'false',
        'icon' => 'icon-tools',
        'iconclass' => 'caret',
        'name' => gT('Tools'),
    ],
    'dropdown' => [
        'class' => 'dropdown-menu',
        'arialabelledby' => 'ls-tools-button',
        'items' => [],
    ],
];

if ($hasDeletePermission) {
    $buttons['delete_survey'] = [
        'url' => $this->createUrl("admin/survey/sa/delete/surveyid/{$sid}"),
        'icon' => 'fa fa-trash',
        'name' => gT('Delete survey'),
    ];
    array_push($buttonsgroup['tools']['dropdown']['items'], $buttons['delete_survey']);
}

if ($hasSurveyTranslatePermission) {
    if ($hasAdditionalLanguages) {
        // Quick-translation
        $buttons['quick_translation'] = [
            'url' => $this->createUrl("admin/translate/sa/index/surveyid/{$sid}"),
            'icon' => 'fa fa-language',
            'name' => gT('Quick-translation'),
        ];
        array_push($buttonsgroup['tools']['dropdown']['items'], $buttons['quick_translation']);
    } else {
        // Quick-translation disabled
        // TODO: In Vue onClick Alert hinzufügen
        $buttons['quick_translation'] = [
            'url' => '#',
            'type' => 'alert',
            'alerttext' => gT('Currently there are no additional languages configured for this survey.'),
            'icon' => 'fa fa-language',
            'name' => gT('Quick-translation'),
        ];
        array_push($buttonsgroup['tools']['dropdown']['items'], $buttons['quick_translation']);
    }
}

if ($hasSurveyContentPermission) {
    if ($conditionsCount > 0) {
        // Condition
        $buttons['reset_conditions'] = [
            'url' => $this->createUrl("/admin/conditions/sa/index/subaction/resetsurveylogic/surveyid/{$sid}"),
            'icon' => 'icon-resetsurveylogic',
            'name' => gT("Reset conditions"),
        ];
        array_push($buttonsgroup['tools']['dropdown']['items'], $buttons['reset_conditions']);
    } else {
        // Condition disabled
        // TODO: alert onlick vue
        $buttons['reset_conditions'] = [
            'url' => '#',
            'type' => 'alert',
            'alerttext' => gT("Currently there are no conditions configured for this survey."),
            'icon' => 'icon-resetsurveylogic',
            'name' => gT("Reset conditions"),
        ];
        array_push($buttonsgroup['tools']['dropdown']['items'], $buttons['reset_conditions']);
    }
}

// TODO: extraToolsMenuItems Plugin?
// TODO: menues from database

if ($hasSurveyReadPermission) {
    if ($oneLanguage) {
        // Survey Logic File
        $buttons['survey_logic_file'] = [
            'url' => $this->createUrl("admin/expressions/sa/survey_logic_file/sid/$sid/"),
            'icon' => 'icon-expressionmanagercheck',
            'name' => gT("Survey logic file"),
        ];
        array_push($buttonsgroup['tools']['dropdown']['items'], $buttons['survey_logic_file']);
    } else {
        // Multiple languages
        // TODO: Multiple languages
    }
}

if (!$isActive && $hasSurveyContentPermission) {
    // Divider
    $buttons['divider'] = [
        'role' => 'seperator',
        'class' => 'divider',
    ];
    array_push($buttonsgroup['tools']['dropdown']['items'], $buttons['divider']);

    // Regenerate question codes
    $buttons['question_codes'] = [
        'class' => 'dropdown-header',
        'name' => gT('Regenerate question codes'),
    ];
    array_push($buttonsgroup['tools']['dropdown']['items'], $buttons['question_codes']);

    // Straight
    $buttons['straight'] = [
        'url' => $this->createUrl("/admin/survey/sa/regenquestioncodes/surveyid/{$sid}/subaction/straight"),
        'icon' => 'icon-resetsurveylogic',
        'name' => gT('Straight'),
    ];
    array_push($buttonsgroup['tools']['dropdown']['items'], $buttons['straight']);

    // By Question Group
    $buttons['by_question_group'] = [
        'url' => $this->createUrl("/admin/survey/sa/regenquestioncodes/surveyid/{$sid}/subaction/bygroup"),
        'name' => gT('By question group'),
        'icon' => 'icon-resetsurveylogic',
    ];

    array_push($buttonsgroup['tools']['dropdown']['items'], $buttons['by_question_group']);
}
array_push($topbar['alignment']['left']['buttons'], $buttonsgroup['tools']);

// Token
if ($hasSurveyTokensPermission) {
    $buttons['survey_participants'] = [
        'url' => $this->createUrl("admin/tokens/sa/index/surveyid/$sid"),
        'class' => 'pjax btntooltip',
        'icon' => 'fa fa-user',
        'name' => gT('Survey participants'),
    ];
    array_push($topbar['alignment']['left']['buttons'], $buttons['survey_participants']);
}

// Statistics
if ($isActive) {
    $buttonsgroup['statistics'] = [
        'class' => 'btn-group',
        'main_button' => [
            'class' => 'dropdown-toggle',
            'datatoggle' => 'dropdown',
            'ariahaspopup' => true,
            'ariaexpanded' => false,
            'icon' => 'icon-responses',
            'name' => gT('Responses'),
            'iconclass' => 'caret',
        ],
        'dropdown' => [
            'class' => 'dropdown-menu',
            'items' => [],
        ],
    ];

    // Responses & statistics
    if (isset($respstatsread) && $respstatsread && $isActive) {
        $buttons['responses_statistics'] = [
            'class' => 'pjax',
            'url' => $this->createUrl("admin/responses/sa/index/surveyid/$sid/"),
            'icon' => 'icon-browse',
            'name' => gT('Responses & statistics'),
        ];

        array_push($buttonsgroup['statistics']['dropdown']['items'], $buttons['responses_statistics']);
    }

    // Data Entry Screen
    if ($hasResponsesCreatePermission && $isActive) {
        $buttons['data_entry_screen'] = [
            'url' => $this->createUrl("admin/dataentry/sa/view/surveyid/$sid"),
            'icon' => 'fa fa-keyboard-o',
            'name' => gT('Data entry screen'),
        ];

        array_push($buttonsgroup['statistics']['dropdown']['items'], $buttons['data_entry_screen']);
    }

    // Partial (saved) Responses
    if ($hasResponsesReadPermission && $isActive) {
        $buttons['partial_saved_responses'] = [
            'url' => $this->createUrl("admin/saved/sa/view/surveyid/$sid"),
            'icon' => 'icon-saved',
            'name' => gT('Partial (saved) responses'),
        ];

        array_push($buttonsgroup['statistics']['dropdown']['items'], $buttons['partial_saved_responses']);
    }

} else {
    $button_statistics = [
        'class' => 'readonly',
        'url' => '#',
        'name' => gT('Responses'),
        'icon' => 'icon-responses',
        'datatoggle' => 'tooltip',
        'dataplacement' => 'bottom',
        'title' => gT('This survey is not active - no responses are available.')
    ];

    array_push($topbar['alignment']['left']['buttons'], $button_statistics);
}

$buttons['save'] = [
    'name' => gT('Save'),
    'id' => 'save-button',
    'class' => 'btn-success',
    'icon' => 'fa fa-floppy-o',
    'url' => '#',
    'isSaveButton' => true
];
array_push($topbar['alignment']['right']['buttons'], $buttons['save']);


$finalJSON = [
    'permissions' => $permissions,
    'topbar' => $topbar,
];

header('Content-Type: application/json');
echo json_encode($finalJSON);
