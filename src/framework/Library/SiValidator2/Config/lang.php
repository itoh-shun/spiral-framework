<?php

const SIVALIDATELANG = [
    'ja' => [
        "required" => ":attribute は必須です。",
        "accepted" => ':attribute を承認してください。',
        "accepted_if" => ':other が :value の場合、:attribute を承認してください。',
        "after" => ":date 以降の日付である必要があります。",
        "after_or_equal" => ":date 以降または同じ日付である必要があります。",
        "before" => ":date 以前の日付である必要があります。",
        "before_or_equal" => ":date 以前または同じ日付である必要があります。",
        "date" => ":attribute は有効な日付でなければなりません。",
        "date_equals" => ":attribute は :date と同じ日付でなければなりません。",
        "date_format" => ":attribute は :format の形式でなければなりません。",
        "alpha" => ":attribute はアルファベット文字でなければなりません。",
        "alpha_dash" => ":attribute はアルファベット、数字、ダッシュ、アンダースコアのみを含むことができます。",
        "alpha_num" => ":attribute はアルファベットと数字のみを含むことができます。",
        "active_url" => ':attribute は有効なURLではありません。',
        "between" => ":attribute は :min から :max の間でなければなりません。",
        "boolean" => ":attribute は、true、false、1、0、'1'、'0'のいずれかでなければなりません。",
        "confirmed" => ":attribute の確認が一致しません。",
        "declined" => ":attribute は「no」、「off」、0、falseである必要があります。",
        "declined_if" => ":otherが:valueの場合、:attribute は「no」、「off」、0、falseである必要があります。",
        "different" => ":attribute は :field と異なる必要があります。",
        "digits" => ":attribute は :value 桁である必要があります。",
        "email" => ":attribute は有効なEメールアドレスではありません。",
        'exists' => ':attribute が存在しません。',
        'unique' => ':attribute は既に存在します。',
        'timezone' => ':attribute は有効なタイムゾーンである必要があります。',
        'string' => ':attribute は文字列である必要があります。',
        'numeric' => ':attribute は数値である必要があります。',
        'min' => ':attribute は最低 :value である必要があります。',
        'max' => ':attribute は :value より大きくてはいけません。',
        'max_bytes' => ':attribute は :value 文字より大きくてはいけません。',
        'json' => ':attribute は有効なJSON文字列である必要があります。',
        'integer' => ':attribute は整数である必要があります。',
    ],
    'en' => [
        "required" => ":attribute is required.",
        "accepted" => "please approve :attribute .",
        "accepted_if" => "The :attribute must be accepted when :other is :value.",
        "after" => "The date must be after :date.",
        "after_or_equal" => "The date must be on or after :date.",
        "before" => "The date must be before :date.",
        "before_or_equal" => "The date must be on or before :date.",
        "date" => "The :attribute must be a valid date.",
        "date_equals" => "The :attribute must be equal to :date.",
        "date_format" => "The :attribute must match the format :format.",
        "alpha" => "The :attribute may only contain letters.",
        "alpha_dash" => "The :attribute may only contain letters, numbers, dashes, and underscores.",
        "alpha_num" => "The :attribute may only contain letters and numbers.",
        "active_url" => 'The :attribute is not a valid active URL.',
        "between" => "The :attribute must be between :min and :max.",
        "boolean" => "The :attribute field must be true, false, 1, 0, '1', or '0'.",
        "confirmed" => "The :attribute confirmation does not match.",
        "declined" => ":attribute は「no」、「off」、0、falseである必要があります。",
        "declined_if" => ":otherが:valueの場合、:attribute は「no」、「off」、0、falseである必要があります。",
        "different" => ":attribute は:fieldと異なる必要があります。",
        "digits" => ":attribute は:value桁である必要があります。",
        "email" => ":attribute is not a valid email address.",
        'exists' => ':attribute が存在しません。',
        'unique' => 'The :attribute has already been taken.',
        'timezone' => 'The :attribute must be a valid timezone.',
        'string' => 'The :attribute must be a string.',
        'numeric' => 'The :attribute must be a number.',
        'min' => 'The :attribute must be at least :value.',
        'max' => 'The :attribute may not be greater than :value.',
        'json' => 'The :attribute must be a valid JSON string.',
        'integer' => 'The :attribute must be an integer.',
    ],
];