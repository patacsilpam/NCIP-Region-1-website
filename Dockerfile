FROM wordpress:latest

# Set working directory
WORKDIR /var/www/html

# Copy WordPress files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]