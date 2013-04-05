Feature: data
    As a prototype editor
    I benefit from scraping mechanisms
    To easily reuse existing content

Scenario: scrap & filter
    When I am on "http://prontotype-foo.lo/scraper"
    Then the "#pt-content" element should contain "this will be scraped"
    And  the "#pt-content" element should contain "this should get some injection."
    And  the "#pt-content" element should contain "this also."

Scenario: scrap & inject
    When I am on "http://prontotype-foo.lo/scraper"
    Then the "#pt-content" element should contain "this will be scraped"
    And  the "#pt-content" element should contain "this should get some injection."
    And  I should see 3 "p" elements
    And  I should see "injected this should" in the "#pt-content" element
    And  I should see "injected this also" in the "#pt-content" element

