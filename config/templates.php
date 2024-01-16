<?php
return [
    'hero' => [
        'field_name' => [
            'title' => 'text',
            'sub_title' => 'text',
            'another_sub_title' => 'text',
            'short_description' => 'textarea',
            'button_name' => 'text',
            'image1' => 'file',
            'image2' => 'file',
            'image3' => 'file',
        ],
        'validation' => [
            'title.*' => 'required|max:100',
            'short_description.*' => 'required|max:2000',
            'button_name.*' => 'required|max:2000',
            'background_image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
            'image1.*' => 'nullable|max:10000|image|mimes:jpg,jpeg,png',
            'image2.*' => 'nullable|max:10000|image|mimes:jpg,jpeg,png',
            'image3.*' => 'nullable|max:10000|image|mimes:jpg,jpeg,png',
        ],
        'size' => [
            'image1' => '1279x1279',
            'image2' => '1440x1440',
            'image3' => '1275x1275',
        ]
    ],
    'about-us' => [
        'field_name' => [
            'title' => 'text',
            'sub_title' => 'text',
            'short_title' => 'text',
            'short_description' => 'textarea',
            'image1' => 'file',
            'image2' => 'file'
        ],
        'validation' => [
            'title.*' => 'required|max:100',
            'sub_title.*' => 'required|max:100',
            'short_description.*' => 'required|max:2000',
            'image1.*' => 'nullable|max:10000|image|mimes:jpg,jpeg,png',
            'image2.*' => 'nullable|max:10000|image|mimes:jpg,jpeg,png',
        ],
        'size' => [
            'image1' => '1279x1279',
            'image2' => '1279x1279',
        ]
    ],
    'property' => [
        'field_name' => [
            'title' => 'text',
            'sub_title' => 'text',
        ],
        'validation' => [
            'title.*' => 'required|max:100',
            'sub_title.*' => 'required|max:100',
        ]
    ],
    'testimonial' => [
        'field_name' => [
            'title' => 'text',
            'sub_title' => 'text',
            'image' => 'file'
        ],
        'validation' => [
            'title.*' => 'required|max:100',
            'sub_title.*' => 'required|max:2000',
            'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png'
        ]
    ],
    'latest-property' => [
        'field_name' => [
            'title' => 'text',
            'sub_title' => 'text',
        ],
        'validation' => [
            'title.*' => 'required|max:100',
            'sub_title.*' => 'required|max:100',
        ]
    ],
    'statistics' => [
        'field_name' => [
            'title' => 'text',
            'sub_title' => 'text',
        ],
        'validation' => [
            'title.*' => 'required|max:100',
            'sub_title.*' => 'required|max:100',
        ]
    ],
    'blog' => [
        'field_name' => [
            'title' => 'text',
            'sub_title' => 'text',
        ],
        'validation' => [
            'title.*' => 'required|max:100',
            'sub_title.*' => 'required|max:2000',
        ]
    ],
    'news-letter' => [
        'field_name' => [
            'title' => 'text',
            'sub_title' => 'text'
        ],
        'validation' => [
            'title.*' => 'required|max:100',
            'sub_title.*' => 'required|max:2000'
        ]
    ],
    'faq' => [
        'field_name' => [
            'title' => 'text',
            'sub_title' => 'text',
            'short_details' => 'textarea',
        ],
        'validation' => [
            'title.*' => 'required|max:100',
            'sub_title.*' => 'required|max:100',
            'short_details.*' => 'required|max:2000'
        ]
    ],
    'contact-us' => [
        'field_name' => [
            'heading' => 'text',
            'sub_heading' => 'text',
            'title' => 'text',
            'address' => 'text',
            'email' => 'text',
            'phone' => 'text',
            'footer_short_details' => 'textarea',
            'image' => 'file'
        ],
        'validation' => [
            'heading.*' => 'required|max:100',
            'sub_heading.*' => 'required|max:100',
            'title.*' => 'required|max:100',
            'address.*' => 'required|max:2000',
            'email.*' => 'required|max:2000',
            'phone.*' => 'required|max:2000',
            'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
        ]
    ],
    'maintenance-page' => [
        'field_name' => [
            'title' => 'text',
            'sub_title' => 'text',
            'short_description' => 'textarea',
            'image' => 'file',
        ],
        'validation' => [
            'title.*' => 'required|max:100',
            'sub_title.*' => 'required|max:100',
            'short_description.*' => 'required|max:5000',
            'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
        ]
    ],
    'message' => [
        'required' => 'This field is required.',
        'min' => 'This field must be at least :min characters.',
        'max' => 'This field may not be greater than :max characters.',
        'image' => 'This field must be image.',
        'background_image' => 'This field must be image.',
        'mimes' => 'This image must be a file of type: jpg, jpeg, png',
    ],
    'template_media' => [
        'image' => 'file',
        'image1' => 'file',
        'image2' => 'file',
        'image3' => 'file',
        'background_image' => 'file',
        'thumbnail' => 'file',
        'youtube_link' => 'url',
        'button_link' => 'url',
    ]
];
