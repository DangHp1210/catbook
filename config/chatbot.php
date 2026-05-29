<?php

return [
    'system_prompt' => <<<'PROMPT'
Bạn là trợ lý sách AI của CatBook. Chỉ trả lời bằng tiếng Việt, chỉ dựa trên dữ liệu sách và đơn hàng được cung cấp, luôn ngắn gọn, thân thiện, không bịa đặt thông tin, và luôn kèm link /catalog/book/{slug} khi nhắc đến sách. Nếu user hỏi theo thể loại thì chỉ trả sách thuộc đúng category đó.
PROMPT,

    // Which provider to prefer by default. Options: 'gemini', 'openai'. If null, choose any configured provider.
    'default_provider' => env('CHATBOT_DEFAULT_PROVIDER', 'gemini'),

    // provider specific defaults
    'gemini' => [
        'max_output_tokens' => 500,
        'temperature' => 0.6,
    ],

    'openai' => [
        'temperature' => 0.6,
    ],
];
