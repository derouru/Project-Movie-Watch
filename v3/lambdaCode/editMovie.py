import json
import os
import mysql.connector
from urllib.parse import unquote


def lambda_handler(event, context):
    try:
        # connect to RDS database
        connection = mysql.connector.connect(
            host = os.environ['RDS_HOST'],
            user = os.environ['USER_NAME'],
            password = os.environ['PASSWORD'],
            database = os.environ['DB_NAME']
        )
        cursor = connection.cursor()

        print("Raw event received:", event)
        # take in the parameters from the request
        # Handle both direct invocation and API Gateway proxy
        if 'body' in event:
            # Check if body is already parsed (dict) or needs parsing (str)
            if isinstance(event['body'], str):
                body = json.loads(event['body'])
            else:
                body = event['body']  # Already a dictionary
        else:
            body = event  # Direct invocation case

        # Extract parameters with validation
        try:
            movie = int(body.get('movie_id', 0))
            user = int(body.get('user_id', 0))
            name = str(body.get('name', ''))
            watch = str(body.get('watched', ''))
        except (ValueError, AttributeError) as e:
            raise ValueError(f"Invalid parameter format: {str(e)}")

        # Validate required fields
        if movie <= 0 or user <= 0:
            raise ValueError("IDs must be positive integers")
        if not name or not watch:
            raise ValueError("name and watched are required")
        
        # query to add
        edit_query = "UPDATE movies SET name = %s, watched = %s WHERE movie_id = %s AND user_id = %s";
        cursor.execute(edit_query, (name, watch, movie, user))

        if cursor.rowcount == 0:
            raise ValueError("No movie found with that ID for this user")

        connection.commit()

        cursor.close()
        connection.close()

        return {
                'statusCode': 200,
                'body': json.dumps({
                    'message': 'Movie edited successfully',
                })
            }
    except Exception as e:
        print("Event:", event)
        print("Error:", str(e))
        return {
            'statusCode': 500,
            'body': json.dumps({
                'error': str(e),
                'message': 'Failed to edit movie'
            })
        }