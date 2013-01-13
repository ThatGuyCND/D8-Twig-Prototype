Feature: data
    As a prototype editor
    I benefit from data mechanisms
    To easily manage and render structured data in my prototype

Scenario Outline: Page IDs
    Given I am on "/animals/birds"
    Then the "#<type>" element should contain "<surname>"

    Examples:
        | type   | surname |
        | csv    | casanova|
        | json   | jason   |
        | xml    | xamilo  |
        | yaml   | yamal   |
