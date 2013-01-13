Feature: users
    As a prototype editor
    I benefit from users/login mechanisms

Scenario Outline: login/logout
    Given I go to "/_login?user=<user>"
    When I go to "/animals/reptiles"
    Then the "#user" element should contain "<name>"
    Given I go to "/_logout"
    When I go to "/animals/reptiles"
    Then the "#user" element should not contain "<name>"

    Examples:
        | user  | name          |
        | user1 | Edward Xample |
        | user2 | Ellie Xample  |
