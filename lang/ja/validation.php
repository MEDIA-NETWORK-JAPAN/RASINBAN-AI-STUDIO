<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (Japanese)
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class.
    |
    */

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeは有効なURLではありません。',
    'after' => ':attributeは:dateより後の日付を指定してください。',
    'after_or_equal' => ':attributeは:date以降の日付を指定してください。',
    'alpha' => ':attributeは英字のみ使用できます。',
    'alpha_dash' => ':attributeは英数字とダッシュ、アンダースコアのみ使用できます。',
    'alpha_num' => ':attributeは英数字のみ使用できます。',
    'array' => ':attributeは配列でなければなりません。',
    'ascii' => ':attributeは半角英数字と記号のみ使用できます。',
    'before' => ':attributeは:dateより前の日付を指定してください。',
    'before_or_equal' => ':attributeは:date以前の日付を指定してください。',
    'between' => [
        'array' => ':attributeは:min個から:max個の間で指定してください。',
        'file' => ':attributeは:min KBから:max KBの間のファイルサイズにしてください。',
        'numeric' => ':attributeは:minから:maxの間で指定してください。',
        'string' => ':attributeは:min文字から:max文字の間で指定してください。',
    ],
    'boolean' => ':attributeはtrueかfalseを指定してください。',
    'can' => ':attributeに許可されていない値が含まれています。',
    'confirmed' => ':attributeが確認用と一致しません。',
    'contains' => ':attributeに必要な値が含まれていません。',
    'current_password' => '現在のパスワードが正しくありません。',
    'date' => ':attributeは有効な日付ではありません。',
    'date_equals' => ':attributeは:dateと同じ日付を指定してください。',
    'date_format' => ':attributeは:format形式で指定してください。',
    'decimal' => ':attributeは小数点以下:decimal桁で指定してください。',
    'declined' => ':attributeを拒否してください。',
    'declined_if' => ':otherが:valueの場合、:attributeを拒否してください。',
    'different' => ':attributeと:otherは異なる値を指定してください。',
    'digits' => ':attributeは:digits桁で指定してください。',
    'digits_between' => ':attributeは:min桁から:max桁の間で指定してください。',
    'dimensions' => ':attributeの画像サイズが無効です。',
    'distinct' => ':attributeに重複した値があります。',
    'doesnt_end_with' => ':attributeは次のいずれかで終わってはいけません: :values',
    'doesnt_start_with' => ':attributeは次のいずれかで始まってはいけません: :values',
    'email' => '正しい:attributeを入力してください',
    'ends_with' => ':attributeは次のいずれかで終わる必要があります: :values',
    'enum' => '選択された:attributeが無効です。',
    'exists' => '選択された:attributeは無効です。',
    'extensions' => ':attributeは次の拡張子のファイルである必要があります: :values',
    'file' => ':attributeはファイルである必要があります。',
    'filled' => ':attributeは必須です。',
    'gt' => [
        'array' => ':attributeは:value個より多い必要があります。',
        'file' => ':attributeは:value KBより大きい必要があります。',
        'numeric' => ':attributeは:valueより大きい必要があります。',
        'string' => ':attributeは:value文字より多い必要があります。',
    ],
    'gte' => [
        'array' => ':attributeは:value個以上である必要があります。',
        'file' => ':attributeは:value KB以上である必要があります。',
        'numeric' => ':attributeは:value以上である必要があります。',
        'string' => ':attributeは:value文字以上である必要があります。',
    ],
    'hex_color' => ':attributeは有効な16進数カラーコードである必要があります。',
    'image' => ':attributeは画像ファイルである必要があります。',
    'in' => '選択された:attributeは無効です。',
    'in_array' => ':attributeは:otherに存在しません。',
    'integer' => ':attributeは整数で指定してください。',
    'ip' => ':attributeは有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeは有効なIPv4アドレスを指定してください。',
    'ipv6' => ':attributeは有効なIPv6アドレスを指定してください。',
    'json' => ':attributeは有効なJSON文字列を指定してください。',
    'list' => ':attributeはリストである必要があります。',
    'lowercase' => ':attributeは小文字である必要があります。',
    'lt' => [
        'array' => ':attributeは:value個未満である必要があります。',
        'file' => ':attributeは:value KB未満である必要があります。',
        'numeric' => ':attributeは:value未満である必要があります。',
        'string' => ':attributeは:value文字未満である必要があります。',
    ],
    'lte' => [
        'array' => ':attributeは:value個以下である必要があります。',
        'file' => ':attributeは:value KB以下である必要があります。',
        'numeric' => ':attributeは:value以下である必要があります。',
        'string' => ':attributeは:value文字以下である必要があります。',
    ],
    'mac_address' => ':attributeは有効なMACアドレスである必要があります。',
    'max' => [
        'array' => ':attributeは:max個以下にしてください。',
        'file' => ':attributeは:max KB以下のファイルサイズにしてください。',
        'numeric' => ':attributeは:max以下を指定してください。',
        'string' => ':attributeは:max文字以下にしてください。',
    ],
    'max_digits' => ':attributeは:max桁以下である必要があります。',
    'mimes' => ':attributeは次のファイルタイプである必要があります: :values',
    'mimetypes' => ':attributeは次のファイルタイプである必要があります: :values',
    'min' => [
        'array' => ':attributeは:min個以上にしてください。',
        'file' => ':attributeは:min KB以上のファイルサイズにしてください。',
        'numeric' => ':attributeは:min以上を指定してください。',
        'string' => ':attributeは:min文字以上にしてください。',
    ],
    'min_digits' => ':attributeは:min桁以上である必要があります。',
    'missing' => ':attributeは存在してはいけません。',
    'missing_if' => ':otherが:valueの場合、:attributeは存在してはいけません。',
    'missing_unless' => ':otherが:valueでない場合、:attributeは存在してはいけません。',
    'missing_with' => ':valuesが存在する場合、:attributeは存在してはいけません。',
    'missing_with_all' => ':valuesが全て存在する場合、:attributeは存在してはいけません。',
    'multiple_of' => ':attributeは:valueの倍数である必要があります。',
    'not_in' => '選択された:attributeは無効です。',
    'not_regex' => ':attributeの形式が無効です。',
    'numeric' => ':attributeは数値を指定してください。',
    'password' => [
        'letters' => ':attributeは少なくとも1文字の英字を含む必要があります。',
        'mixed' => ':attributeは少なくとも1文字の大文字と小文字を含む必要があります。',
        'numbers' => ':attributeは少なくとも1文字の数字を含む必要があります。',
        'symbols' => ':attributeは少なくとも1文字の記号を含む必要があります。',
        'uncompromised' => '指定された:attributeはデータ漏洩で見つかりました。別の:attributeを選択してください。',
    ],
    'present' => ':attributeは存在している必要があります。',
    'present_if' => ':otherが:valueの場合、:attributeは存在している必要があります。',
    'present_unless' => ':otherが:valueでない場合、:attributeは存在している必要があります。',
    'present_with' => ':valuesが存在する場合、:attributeは存在している必要があります。',
    'present_with_all' => ':valuesが全て存在する場合、:attributeは存在している必要があります。',
    'prohibited' => ':attributeは禁止されています。',
    'prohibited_if' => ':otherが:valueの場合、:attributeは禁止されています。',
    'prohibited_unless' => ':otherが:valuesに含まれない場合、:attributeは禁止されています。',
    'prohibits' => ':attributeが存在する場合、:otherは禁止されています。',
    'regex' => ':attributeの形式が無効です。',
    'required' => ':attributeを入力してください',
    'required_array_keys' => ':attributeには次のキーが必要です: :values',
    'required_if' => ':otherが:valueの場合、:attributeを入力してください。',
    'required_if_accepted' => ':otherが承認された場合、:attributeを入力してください。',
    'required_if_declined' => ':otherが拒否された場合、:attributeを入力してください。',
    'required_unless' => ':otherが:valuesに含まれない場合、:attributeを入力してください。',
    'required_with' => ':valuesが存在する場合、:attributeを入力してください。',
    'required_with_all' => ':valuesが全て存在する場合、:attributeを入力してください。',
    'required_without' => ':valuesが存在しない場合、:attributeを入力してください。',
    'required_without_all' => ':valuesが全て存在しない場合、:attributeを入力してください。',
    'same' => ':attributeと:otherは一致する必要があります。',
    'size' => [
        'array' => ':attributeは:size個にしてください。',
        'file' => ':attributeは:size KBにしてください。',
        'numeric' => ':attributeは:sizeを指定してください。',
        'string' => ':attributeは:size文字にしてください。',
    ],
    'starts_with' => ':attributeは次のいずれかで始まる必要があります: :values',
    'string' => ':attributeは文字列を指定してください。',
    'timezone' => ':attributeは有効なタイムゾーンを指定してください。',
    'unique' => ':attributeはすでに使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeは大文字である必要があります。',
    'url' => ':attributeは有効なURLを指定してください。',
    'ulid' => ':attributeは有効なULIDである必要があります。',
    'uuid' => ':attributeは有効なUUIDである必要があります。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email".
    |
    */

    'attributes' => [
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'name' => '名前',
    ],

];
