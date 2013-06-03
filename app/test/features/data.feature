Feature: data
    As a prototype editor
    I benefit from data mechanisms
    To easily manage and render structured data in my prototype

Scenario Outline: Accessing data in templates
        When I am on "http://prontotype-foo.lo/data-test"
    Then the "#<type>" element should contain "<surname>"

    Examples:
        | type   | surname |
        | csv    | casanova|
        | json   | jason   |
        | xml    | xamilo  |
        | yaml   | yamal   |

Scenario Outline: Serving data for AJAX requests
    When I am on "http://prontotype-foo.lo/_data/<type>"
    Then the response should contain "<surname>"

    Examples:
        | type   | surname |
        | csv    | casanova|
        | json   | jason   |
        | xml    | xamilo  |
        | yaml   | yamal   |

Scenario Outline: drilling down structured data in AJAX
    When I am on "http://prontotype-foo.lo/_data/<type>/people/pat"
    Then the response should contain "Postman"
    And the response should not contain "<surname>"

    Examples:
        | type   | surname |
        | json   | jason   |
        | xml    | xamilo  |
        | yaml   | yamal   |

Scenario Outline: drilling down relational data in AJAX
    When I am on "http://prontotype-foo.lo/_data/<type>/pat/last_name"
    Then the response should contain "Postman"
    And the response should not contain "<surname>"

    Examples:
        | type   | surname |
        | csv    | casanova|
