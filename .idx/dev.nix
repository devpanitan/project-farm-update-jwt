{ pkgs, ... }:
{
  # Add PHP and Node.js to the environment
  packages = [
    pkgs.php
    pkgs.nodejs_20 # Using LTS version for better compatibility
  ];

  # Enable previews and customize configuration
  idx.previews = {
    enable = true;
    previews = {
      # This sets up the web preview
      web = {
        # This command starts the development servers
        command = [ "npm" "run" "dev" ];
        manager = "web";
      };
    };
  };
}
