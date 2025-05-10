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

        # take in the parameters from the request
        user = int(event["user_id"])
        watch = unquote(event["watched"])
        name = event["name"]

        if not (user and watch and name):
            raise ValueError("Missing required query parameters")
        
        # query to add
        insert_query = "INSERT INTO movies (name, watched, user_id) VALUES (%s, %s, %s)"
        cursor.execute(insert_query, (name, watch, user))
        connection.commit()

        cursor.close()
        connection.close()

        return {
                'statusCode': 200,
                'body': json.dumps({
                    'message': 'Movie added successfully',
                })
            }
    except Exception as e:
        print("Event:", event)
        print("Error:", str(e))
        return {
            'statusCode': 500,
            'body': json.dumps({
                'error': str(e),
                'message': 'Failed to add movie'
            })
        }