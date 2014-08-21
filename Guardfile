notification :off

group :development do

  # Only run Compass if we have a config.rb file in place.
  if File.exists?("config.rb")
    # Compile on start.
    puts `compass compile --time --quiet`

    # https://github.com/guard/guard-compass
    guard :compass do
      watch(%r{.+\.s[ac]ss$})
    end
  end
  
  gem 'guard-livereload', require: false

  # https://github.com/guard/guard-livereload.
  # Ignore *.normalize.scss to prevent flashing content when re-rendering.
  guard :livereload do
    watch(%r{^((?!\.normalize\.).)*\.(css|js)$})
  end

end

guard 'livereload', notify: true, host: 'twig.pt' do
  watch(%r{^((?!\.normalize\.).)*\.(css|js)$})
end
