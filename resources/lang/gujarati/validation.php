<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'આ :attribute સ્વીકારવી આવશ્યક છે.',
    'active_url' => 'આ :attribute માન્ય URL નથી.',
    'after' => ':attribute ની તારીખ :date પછી હોવી જોઈએ.',
    'after_or_equal' => ':attribute ની તારીખ :date પછી અથવા તેમને સમાં રાખવી જોઈએ.',
    'alpha' => ':attribute માં ફક્ત અક્ષરો હોઈ જોઈએ.',
    'alpha_dash' => ':attribute માં ફક્ત અક્ષરો, અંકો, ડેશ અને આંડરસ્કોર્સ હોઈ જોઈએ.',
    'alpha_num' => ':attribute માં ફક્ત અક્ષરો અને અંકો હોઈ જોઈએ.',
    'array' => ':attribute એક એરે હોવું જોઈએ.',
    'before' => ':attribute ની તારીખ :date પહેલા હોવી જોઈએ.',
    'before_or_equal' => ':attribute ની તારીખ :date પહેલા અથવા તેમને સમાં રાખવી જોઈએ.',
    'between' => [
        'numeric' => ':attribute માં :min અને :max વચ્ચે હોવું જોઈએ.',
        'file' => ':attribute માં :min અને :max કિલોબાઇટ વચ્ચે હોવું જોઈએ.',
        'string' => ':attribute :min અને :max અક્ષરો વચ્ચે હોવું જોઈએ.',
        'array' => ':attribute :min અને :max આઇટમ્સ વચ્ચે હોવું જોઈએ.',
    ],
    'boolean' => ':attribute ક્ષેત્ર સાચું અથવા ખોટું હોવું જોઈએ.',
    'confirmed' => ':attribute પુષ્ટિકરણ મેળ નથી ખાતો.',
    'current_password' => 'પાસવર્ડ ખોતું છે.',
    'date' => ':attribute માં માન્ય તારીખ નથી.',
    'date_equals' => ':attribute :date સાથે બરાબરી તારીખ હોવી જોઈએ.',
    'date_format' => ':attribute :format ફોર્મેટ સાથે મેળ નથી ખાતો.',
    'different' => ':attribute અને :other વચ્ચે અલગ હોવું જોઈએ.',
    'digits' => ':attribute :digits અંકો હોવું જોઈએ.',
    'digits_between' => ':attribute :min અને :max અંકો વચ્ચે હોવું જોઈએ.',
    'dimensions' => ':attribute ગુમાના માપદણા અસત્યાંક છે.',
    'distinct' => ':attribute ક્ષેત્રમાં એક ટુંક મૂલ્ય છે.',
    'email' => ':attribute મેળવેલ ઈમેઇલ સરનામું હોવું જોઈએ.',
    'ends_with' => ':attribute નો શેષ થવો જોઈએ એમનામું: :values માંથી એક સાથે.',
    'exists' => 'પસંદ કરેલો :attribute અમાન્ય છે.',
    'file' => ':attribute એક ફાઇલ હોવી જોઈએ.',
    'filled' => ':attribute ક્ષેત્રમાં મૂલ્ય હોવી જોઈએ.',
    'gt' => [
        'numeric' => ':attribute મોટું હોવું જોઈએ :value કરતા વધુ.',
        'file' => ':attribute મોટું હોવું જોઈએ :value કિલોબાઇટ કરતા વધુ.',
        'string' => ':attribute મોટું હોવું જોઈએ :value અક્ષરો કરતા વધુ.',
        'array' => ':attribute મોટું હોવું જોઈએ :value આઇટમ્સ કરતા વધુ.',
    ],
    'gte' => [
       'numeric' => ':attribute મોટું અથવા સમાન હોવું જોઈએ :value કરતા વધુ.',
        'file' => ':attribute મોટું અથવા સમાન હોવું જોઈએ :value કિલોબાઇટ કરતા વધુ.',
        'string' => ':attribute મોટું અથવા સમાન હોવું જોઈએ :value અક્ષરો કરતા વધુ.',
        'array' => ':attribute માટે :value આઇટમ્સ અથવા તેથી વધુ આવશે.',
    ],
        'image' => ':attribute એ છબી હોવી જોઈએ.',
        'in' => 'પસંદ કરેલું :attribute અમાન્ય છે.',
        'in_array' => ':attribute ફિલ્ડ :other માં અસ્તિત્વ ન ધરાવે છે.',
        'integer' => ':attribute એ પૂર્ણાંક હોવો જોઈએ.',
        'ip' => ':attribute એ માન્ય આઈપી સરનામું હોવું જોઈએ.',
        'ipv4' => ':attribute એ માન્ય IPv4 સરનામું હોવું જોઈએ.',
        'ipv6' => ':attribute એ માન્ય IPv6 સરનામું હોવું જોઈએ.',
        'json' => ':attribute એ માન્ય JSON સિટ્રિંગ હોવી જોઈએ.',
    'lt' => [
             'numeric' => ':attribute :value થી ઓછું હોવો જોઈએ.',
            'file' => ':attribute :value કિલોબાઇટ્સ થી ઓછું હોવું જોઈએ.',
            'string' => ':attribute :value અક્ષરોથી ઓછું હોવું જોઈએ.',
            'array' => ':attribute :value આઇટમ્સથી ઓછું હોવું જોઈએ.',
    ],
    'lte' => [
       'numeric' => ':attribute :value કે તેમ ઓછું હોઈ જોઈએ.',
        'file' => ':attribute :value કિલોબાઇટ્સ કે તેમ ઓછું હોવું જોઈએ.',
        'string' => ':attribute :value અક્ષરો કે તેમ ઓછું હોવું જોઈએ.',
        'array' => ':attribute :value આઇટમ્સ કે તેમ ઓછી હોઈ જોઈએ.',
    ],
    'max' => [
        'numeric' => ':attribute :max કર્તવ્યના ન હોઈ.',
        'file' => ':attribute :max કિલોબાઇટ્સ કર્તવ્યના ન હોઈ.',
        'string' => ':attribute :max અક્ષરો કર્તવ્યના ન હોઈ.',
        'array' => ':attribute :max આઇટમ્સ કર્તવ્યના ન હોઈ.',
    ],
      'mimes' => ':attribute :values પ્રકારનો ફાઈલ હોવી જોઈએ.',
     'mimetypes' => ':attribute :values પ્રકારનો ફાઈલ હોવી જોઈએ.',
    'min' => [
        'numeric' => ':attribute :min ના ઓછું હોવું જોઈએ.',
        'file' => ':attribute :min કિલોબાઇટનો ઓછું હોવું જોઈએ.',
        'string' => ':attribute :min અક્ષરોથી ઓછું હોવું જોઈએ.',
        'array' => ':attribute માં ઓછામાં ઓછું :min આઇટમ્સ હોવી જોઈએ.',
    ],
    'multiple_of' => ':attribute :value નો એક વર્ગાકાર હોવું જોઈએ.',
    'not_in' => 'પસંદ કરેલો :attribute અમાન્ય છે.',
    'not_regex' => ':attribute ફોર્મેટ અમાન્ય છે.',
    'numeric' => ':attribute એક નંબર હોવું જોઈએ.',
    'password' => 'પાસવર્ડ અમાન્ય છે.',
    'present' => ':attribute ફીલ્ડ હાજર હોવું જોઈએ.',
    'regex' => ':attribute ફોર્મેટ અમાન્ય છે.',
    'required' => ':attribute ફીલ્ડ આવશ્યક છે.',
    'required_if' => ':attribute ફીલ્ડ :other છે ત્યારે :value આવશ્યક છે.',
    'required_unless' => ':attribute ફીલ્ડ :values માંથી બાહર હોવી જોઈએ.',
    'required_with' => ':attribute ફીલ્ડ :values હાજર હોવું જોઈએ.',
    'required_with_all' => ':attribute ફીલ્ડ :values હાજર હોવું જોઈએ.',
    'required_without' => ':attribute ફીલ્ડ :values હાજર ન હોય તો જોઈએ.',
    'required_without_all' => ':attribute ફીલ્ડ :values માંથી બાહર હોવું જોઈએ.',
    'prohibited' => ':attribute ફીલ્ડ નિષિદ્ધ છે.',
   'prohibited_if' => ':attribute ફીલ્ડ :other :value છે ત્યારે નિષિદ્ધ છે.',
    'prohibited_unless' => ':attribute ફીલ્ડ :values માંથી બાહર હોવું જોઈએ.',
    'same' => ':attribute અને :other મેળ ખાવો જોઈએ.',
    'size' => [
       'numeric' => ':attribute મોટો હોવો જોઈએ :size.',
        'file' => ':attribute :size કિલોબાઇટ હોવો જોઈએ.',
        'string' => ':attribute :size અક્ષરો હોવો જોઈએ.',
        'array' => ':attribute :size આઇટમ્સ સાથે હોવી જોઈએ.',
    ],
    'starts_with' => ':attribute ની પ્રારંભમાં ની થઈ છે :values માંથું એક સાથ.',
    'string' => ':attribute એક સરસ સ્ટ્રિંગ હોવી જોઈએ.',
    'timezone' => ':attribute એક માન્ય ટાઇમઝોન હોવો જોઈએ.',
    'unique' => ':attribute પહેલેથી લીધો છે.',
    'uploaded' => ':attribute અપલોડ કરવામાં અસમર્થ રહ્યો છે.',
    'url' => ':attribute માન્ય URL હોવું જોઈએ.',
    'uuid' => ':attribute માન્ય UUID હોવો જોઈએ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
